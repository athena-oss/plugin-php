function testcase_athena.plugins.php.is_php_version_available()
{
	bashunit.test.assert_exit_code.expects_fail "athena.plugins.php.is_php_version_available" ""

	local fake_docker_dir="$(bashunit.test.create_tempdir)"
	bashunit.test.mock.outputs "athena.plugin.get_plg_docker_dir" "$fake_docker_dir"

	mkdir "$fake_docker_dir/php7.0"

	bashunit.test.assert_return.expects_fail "athena.plugins.php.is_php_version_available" "5.4"
	bashunit.test.assert_return "athena.plugins.php.is_php_version_available" "7.0"

	rm -r "$fake_docker_dir"
}

function testcase_athena.plugins.php.get_supported_php_versions()
{
	local fake_docker_dir="$(bashunit.test.create_tempdir)"
	mkdir "$fake_docker_dir/garbage"
	mkdir "$fake_docker_dir/php5.4"
	mkdir "$fake_docker_dir/php5.6"
	mkdir "$fake_docker_dir/php7.0"

	bashunit.test.mock.outputs "athena.plugin.get_plg_docker_dir" "$fake_docker_dir"
	bashunit.test.assert_output "athena.plugins.php.get_supported_php_versions" "php5.4, php5.6, php7.0"
	bashunit.test.assert_output.expects_fail "athena.plugins.php.get_supported_php_versions" "garbage, php5.4, php5.6, php7.0"

	rm -r "$fake_docker_dir"
}

function testcase_athena.plugins.php.set_php_version()
{
	bashunit.test.assert_exit_code.expects_fail "athena.plugins.php.set_php_version" ""
	bashunit.test.mock.returns "athena.plugins.php.is_php_version_available" 1
	bashunit.test.mock "athena.os.exit_with_msg" "_void"
	bashunit.test.assert_return.expects_fail "athena.plugins.php.set_php_version"

	bashunit.test.mock.returns "athena.plugins.php.is_php_version_available" 0
	bashunit.test.mock "athena.color.print_debug" "_void"
	bashunit.test.mock "athena.plugin.use_container" "_echo_arguments"
	bashunit.test.assert_output "athena.plugins.php.set_php_version" "php5.6" "5.6"
}

function testcase_athena.plugins.php.disable_debugger_in_container()
{
	bashunit.test.mock "athena.docker.add_env" "_echo_arguments"
	bashunit.test.assert_output "athena.plugins.php.disable_debugger_in_container" "ATHENA_DISABLE_DEBUG 1"
}

function testcase_athena.plugins.php.enable_profiler_in_container()
{
	bashunit.test.mock "athena.docker.add_env" "_echo_arguments"
	bashunit.test.assert_output "athena.plugins.php.enable_profiler_in_container" "ATHENA_ENABLE_PROFILER 1"
}

function _void()
{
	return
}

function _echo_arguments()
{
	echo -n "$@"
}
