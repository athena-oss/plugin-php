athena.os.include_once "$(athena.plugin.get_plg_lib_dir php)/shared/functions.sh"

function testcase_athena.plugins.php.is_debug_mode_enabled()
{
	local old_athena_disable_debug_val="$ATHENA_DISABLE_DEBUG"

	export ATHENA_DISABLE_DEBUG=1
	athena.test.assert_return.expects_fail "athena.plugins.php.is_debug_mode_enabled"

	export ATHENA_DISABLE_DEBUG=
	athena.test.assert_return "athena.plugins.php.is_debug_mode_enabled"

	export ATHENA_DISABLE_DEBUG="$old_athena_disable_debug_val"
}

function testcase_athena.plugins.php.get_debug_port()
{
	local old_athena_xdebug_port=$ATHENA_XDEBUG_PORT

	export ATHENA_XDEBUG_PORT=1234
	athena.test.assert_output "athena.plugins.php.get_debug_port" "1234"

	export ATHENA_XDEBUG_PORT=
	athena.test.assert_output "athena.plugins.php.get_debug_port" "9000"

	export ATHENA_XDEBUG_PORT=$old_athena_xdebug_port
}

function testcase_athena.plugins.php.set_debugger_environment_vars()
{
	athena.test.assert_exit_code.expects_fail "athena.plugins.php.set_debugger_environment_vars"
	athena.test.assert_exit_code.expects_fail "athena.plugins.php.set_debugger_environment_vars" "123.1.1.1"

	local old_xdebug_config="$XDEBUG_CONFIG"
	local old_php_ide_config="$PHP_IDE_CONFIG"
	local old_athena_xdebug_port=$ATHENA_XDEBUG_PORT

	athena.test.mock "athena.color.print_debug" "_void"
	athena.plugins.php.set_debugger_environment_vars "123.1.1" 12345
	athena.test.assert_value "$XDEBUG_CONFIG" "idekey=PHPSTORM remote_enable=1 remote_mode=req remote_port=12345 remote_host=123.1.1 remote_connect_back=0"
	athena.test.assert_value "$PHP_IDE_CONFIG" "serverName=ATHENA"
	athena.test.assert_value "$ATHENA_XDEBUG_PORT" 12345

	export XDEBUG_CONFIG="$old_xdebug_config"
	export PHP_IDE_CONFIG="$old_php_ide_config"
	export ATHENA_XDEBUG_PORT=$old_athena_xdebug_port
}

function testcase_athena.plugins.is_profiling_mode_enabled()
{
	local old_athena_enable_profiler_val="$ATHENA_ENABLE_PROFILER"

	export ATHENA_ENABLE_PROFILER=1
	athena.test.assert_return "athena.plugins.php.is_profiling_mode_enabled"

	export ATHENA_ENABLE_PROFILER=
	athena.test.assert_return.expects_fail "athena.plugins.php.is_profiling_mode_enabled"

	export ATHENA_ENABLE_PROFILER="$old_athena_enable_profiler_val"
}

function testcase_athena.plugins.php.set_profiler_environment_vars()
{
	athena.test.assert_exit_code.expects_fail "athena.plugins.php.set_profiler_environment_vars"

 	local old_xdebug_config="$XDEBUG_CONFIG"

	export XDEBUG_CONFIG=
	athena.test.assert_exit_code.expects_fail "athena.plugins.php.set_profiler_environment_vars" "/opt/tests"

	export XDEBUG_CONFIG="config=value"
	athena.test.mock "athena.color.print_debug" "_void"
	athena.plugins.php.set_profiler_environment_vars "/opt/testsxpto"
	athena.test.assert_value "$XDEBUG_CONFIG" "config=value profiler_enable=1 profiler_output_dir=/opt/testsxpto"

	export XDEBUG_CONFIG="$old_xdebug_config"
}

function _void()
{
	return
}
