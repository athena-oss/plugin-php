if ! athena.argument.argument_exists "--php-version"; then
	athena.plugins.php.set_php_version "5.6"
else
	version="$(athena.argument.get_argument --php-version)"
	athena.argument.remove_argument "--php-version"

	if [[ -z "$version" ]]; then
		athena.fatal "--php-version must be set to one of the available versions: $(athena.plugins.php.get_supported_php_versions)"
	else
		athena.plugins.php.set_php_version "$version"
	fi
fi

if ! athena.plugins.php.is_debug_mode_enabled; then
	athena.plugins.php.disable_debugger_in_container
else
	debug_host="$(athena.os.get_host_ip)"
	debug_port=$(athena.plugins.php.get_debug_port)

	athena.plugins.php.set_debugger_environment_vars "$debug_host" $debug_port

	if athena.plugins.php.is_profiling_mode_enabled; then
		athena.plugins.php.enable_profiler_in_container
	fi
fi

athena.docker.add_envs_with_prefix "ATHENA_ENV"
