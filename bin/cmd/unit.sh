tests_dir="$(athena.arg 1)"
athena.argument.pop_arguments 1

export ATHENA_TESTS_TYPE="unit"
export ATHENA_TESTS_DIRECTORY="."

if athena.argument.argument_exists "-f"; then
	export ATHENA_CONFIGURATION_FILE="$(athena.argument.get_argument "-f")"
	athena.argument.remove_argument "-f"
fi

use_athena_bootstrap=1
if athena.argument.argument_exists "--no-athena-bootstrap"; then
	use_athena_bootstrap=0
	athena.argument.remove_argument "--no-athena-bootstrap"
fi

# check if athena can inject it's own bootstrap file
if ! athena.argument.argument_exists "--bootstrap" && [[ $use_athena_bootstrap -eq 1 ]]; then
	athena.argument.append_to_arguments "--bootstrap=$WORKDIR/bootstrap.php"
fi

phpunit_exit_code=0
arguments=()
athena.argument.get_arguments arguments

pushd "$tests_dir" 1>/dev/nul
if [ ! -f ./phpunit.xml ]; then
	$PHPUNIT_CMD "${arguments[@]}" .
else
	$PHPUNIT_CMD "${arguments[@]}"
fi
phpunit_exit_code=$?
popd 1>/dev/null

if [[ $phpunit_exit_code -ne 0 ]]; then
	athena.os.exit $phpunit_exit_code
fi
