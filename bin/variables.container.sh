WORKDIR=/opt/athena
BEHAT_CMD=$WORKDIR/vendor/bin/behat
PHPCS_CMD=$WORKDIR/vendor/bin/phpcs
PHPMD_CMD=$WORKDIR/vendor/bin/phpmd
PHPUNIT_CMD=$WORKDIR/vendor/bin/phpunit
PHPUNIT_TESTS_CMD=/opt/tests/vendor/bin/phpunit
OVERRIDE_LOCK_FILE=/opt/tests/composer.override

athena_command="$(athena.os.get_command)"

if ! athena.plugins.php.is_debug_mode_enabled || [[ "$athena_command" = "init" ]]; then
	athena.plugins.php.disable_php_extension "xdebug"
else
	debugger_host="$(athena.os.get_host_ip)"
	debugger_port="$(athena.plugins.php.get_debug_port)"
	athena.plugins.php.set_debugger_environment_vars "$debugger_host" "$debugger_port"

	if athena.plugins.php.is_profiling_mode_enabled; then
		athena.plugins.php.set_profiler_environment_vars "/opt/tests"
	fi
fi

# PHPunit
if [[ "$athena_command" =~ .*(unit|browser|api).* ]]  && athena.argument.argument_exists "--parallel"; then
	nr_procs=$(athena.argument.get_argument --parallel)
	athena.argument.append_to_arguments "--processes=${nr_procs}"
	athena.argument.remove_argument "--parallel"
	PHPUNIT_CMD=$WORKDIR/vendor/bin/paratest
elif athena.argument.argument_exists_and_remove "--restore-athena-dependencies" ; then
	athena.plugins.php.restore_default_composer_dependencies
elif athena.argument.argument_exists_and_remove "--override-athena-dependencies" || [ -f $OVERRIDE_LOCK_FILE ]; then

	if [ ! -f /opt/tests/composer.json ]; then
		athena.os.exit_with_msg "cannot override athena dependencies because composer.json does not exist in project folder."
	fi

	athena.plugins.php.override_composer_dependencies
fi
