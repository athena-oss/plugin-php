# arguments were set by api_pre
tests_dir="$(athena.arg 1)"
config_file_path="$(athena.arg 2)"

athena.pop_args 2

# check if athena can inject it's own bootstrap file
if ! athena.argument.argument_exists "--bootstrap"; then
	athena.argument.append_to_arguments "--bootstrap=$WORKDIR/bootstrap.php"
fi

export ATHENA_TESTS_TYPE="api"
export ATHENA_CONFIGURATION_FILE="$config_file_path"
export ATHENA_TESTS_DIRECTORY="."
arguments=()
athena.argument.get_arguments arguments

pushd "$tests_dir" 1>/dev/null

if [[ ! -f ./phpunit.xml ]]; then
	$PHPUNIT_CMD "${arguments[@]}" .
else
	$PHPUNIT_CMD "${arguments[@]}"
fi

phpunit_exit_code=$?
popd 1>/dev/null

if [[ $phpunit_exit_code -ne 0 ]]; then
	athena.os.exit $phpunit_exit_code
fi
