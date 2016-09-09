browser_name="$(athena.argument.arg 1)"
tests_dir="$(athena.argument.arg 2)"
config_file_path="$(athena.argument.arg 3)"

athena.pop_args 3

export ATHENA_TESTS_TYPE="browser"
export ATHENA_BROWSER="$browser_name"
export ATHENA_TESTS_DIRECTORY="$tests_dir"
export ATHENA_CONFIGURATION_FILE="$config_file_path"

# check if athena can inject it's own bootstrap file
if ! athena.argument.argument_exists "--bootstrap" ; then
	athena.argument.append_to_arguments "--bootstrap=$WORKDIR/bootstrap.php"
fi

phpunit_exit_code=0
arguments=()
athena.argument.get_arguments arguments

pushd "$tests_dir" 1>/dev/null
if [ ! -f ./phpunit.xml ]; then
	$PHPUNIT_CMD "${arguments[@]}" .
	phpunit_exit_code=$?
else
	$PHPUNIT_CMD "${arguments[@]}"
	phpunit_exit_code=$?
fi
popd 1>/dev/null

if [[ $phpunit_exit_code -ne 0 ]]; then
	athena.os.exit $phpunit_exit_code
fi
