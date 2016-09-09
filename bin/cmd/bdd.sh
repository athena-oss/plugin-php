tests_dir="$(athena.arg 1)"
config_file_path="$(athena.arg 2)"

athena.pop_args 2

if athena.argument.argument_exists "--init"; then
	if ! athena.plugins.php._behat_init "$tests_dir"; then
		athena.os.exit_with_msg "Failed to initilize behat in ${tests_dir}."
	fi
	return 0
fi

search_dir=${ATHENA_PARALLEL_DIR:-$tests_dir}
parallel_features_nr=0
parallel_scenarios_enabled=0

if athena.argument.argument_exists "--parallel-features"; then
	parallel_features_nr=$(athena.argument.get_argument "--parallel-features")
	if athena.argument.argument_exists "--parallel-process="; then
		parallel_scenarios_enabled=1
	fi
	athena.argument.remove_argument "--parallel-features"
	if athena.argument.argument_exists "--parallel-dir=" ; then
		search_dir=$(athena.argument.get_argument "--parallel-dir")
		athena.argument.remove_argument "--parallel-dir"
	fi
fi

pipe_path="$(mktemp)"

athena.plugins.php._create_pipe "$pipe_path"
athena.plugins.php._register_pipe_cleanup_handler "$pipe_path"

export ATHENA_TESTS_DIRECTORY="."
export ATHENA_TESTS_TYPE="bdd"
export ATHENA_REPORT_PIPE_NAME="$pipe_path"
export ATHENA_CONFIGURATION_FILE="$config_file_path"
export ATHENA_BOOTSTRAP="$WORKDIR/bootstrap.php"
export ATHENA_START_TIMER=$(php -r 'echo microtime(true);')

if athena.argument.argument_exists "--browser"; then
	export ATHENA_BROWSER=$(athena.argument.get_argument "--browser")
	athena.argument.remove_argument "--browser"
fi

pushd "$tests_dir" 1>/dev/null

athena.plugins.php._purge_reports &
athena.plugins.php._init_proxy &
wait

arguments=()
athena.argument.get_arguments arguments

behat_exit_code=0
if [[ $parallel_features_nr -eq 0 ]]; then
	athena.plugins.php._behat "${arguments[@]}"
	behat_exit_code=$?
else
	output_msg="Searching for features in '$search_dir' and starting up to '$parallel_features_nr' features in parallel"
	if [[ $parallel_scenarios_enabled -eq 1 ]]; then
		output_msg=" $output_msg with SCENARIOS also in parallel."
	fi

	athena.color.print_info "$output_msg"
	athena.plugins.php._search_and_exec_features_in_parallel "$search_dir" "$parallel_features_nr" "${arguments[@]}"
	behat_exit_code=$?
fi

athena.plugins.php._aggregate_pipe_output "$pipe_path"

popd 1>/dev/null

if [[ $behat_exit_code -ne 0 ]]; then
	athena.os.exit_with_msg "Behat exited with a non-zero code." $behat_exit_code
fi
