# If you want to disable debug mode set ATHENA_DISABLE_DEBUG=1
# USAGE: athena.plugins.php.is_debug_mode_enabled
# RETURN: 0 (true) or 1 (False)
function athena.plugins.php.is_debug_mode_enabled()
{
	if [[ -n "$ATHENA_DISABLE_DEBUG" ]]; then
		return 1
	fi

	return 0
}

# You can define a custom debug port using ATHENA_XDEBUG_PORT. Default is 9000.
# USAGE: athena.plugins.php.get_debug_port
# RETURN: --
function athena.plugins.php.get_debug_port()
{
	if [[ -n "$ATHENA_XDEBUG_PORT" ]]; then
		echo $ATHENA_XDEBUG_PORT
		return 0
	fi

	echo 9000
	return 0
}

# Set necessary xdebug environment variables for the IDE
# USAGE: athena.plugins.php.set_debugger_environment_vars <debug_host> <debug_port>
# RETURN: --
function athena.plugins.php.set_debugger_environment_vars()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "debug_host"
	athena.argument.argument_is_not_empty_or_fail "$2" "debug_port"

	local debug_host="$1"
	local debug_port=$2

	athena.color.print_debug "PHP XDebug Enabled (${debug_host}:${debug_port})..."

	export XDEBUG_CONFIG="idekey=PHPSTORM remote_enable=1 remote_mode=req remote_port=$debug_port remote_host=$debug_host remote_connect_back=0"
	export PHP_IDE_CONFIG="serverName=ATHENA"
	export ATHENA_XDEBUG_PORT=$debug_port
	return 0
}

# If you want to enable profiling set ATHENA_ENABLE_PROFILER=1
# USAGE: athena.plugins.is_profiling_mode_enabled
# RETURN: 0 (True) or 1 (False)
function athena.plugins.php.is_profiling_mode_enabled()
{
	if [[ -z "$ATHENA_ENABLE_PROFILER" ]]; then
		return 1
	fi

	return 0
}

# Set profiler environment variables. Whatever files the profiler writes, they will be inside output_dir.
# USAGE: athena.plugins.php.set_profiler_environment_vars <output_dir>
# RETURN: --
function athena.plugins.php.set_profiler_environment_vars()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "output_dir"
	local output_dir="$1"

	if [[ -z "$XDEBUG_CONFIG" ]]; then
		athena.os.exit_with_msg "Profiler requires XDEBUG_CONFIG bet set. Make sure athena.plugins.php.set_debugger_environment_vars was called before."
		return 1
	fi

	athena.color.print_debug "PHP XDebug Profiler Enabled..."
	export XDEBUG_CONFIG="$XDEBUG_CONFIG profiler_enable=1 profiler_output_dir=${output_dir}"
	return 0
}
