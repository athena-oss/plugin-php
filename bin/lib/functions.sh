athena.os.include_once "$(athena.plugin.get_plg_lib_dir php)/shared/functions.sh"

# Check whether the given PHP version is available
# USAGE: athena.plugins.php.is_php_version_available <version>
# RETURN: --
function athena.plugins.php.is_php_version_available()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "version"

	local php_version="$1"
	local docker_dir="$(athena.plugin.get_plg_docker_dir php)"

	if [[ ! -d "$docker_dir/php${1}" ]]; then
		return 1
	fi

	return 0
}

# Retrieve PHP versions available
# USAGE: athena.plugins.php.get_supported_php_versions <version>
# RETURN: --
function athena.plugins.php.get_supported_php_versions()
{
	local docker_dir="$(athena.plugin.get_plg_docker_dir php)"

	echo "$(ls -d ${docker_dir}/php* | tr '\n' '\0' | xargs -0 -n 1 basename | tr '\n' ',' | sed 's/,$//' | sed 's/,/, /g')"
	return 0
}

# Switch to a different docker image which contains the desired PHP version
# USAGE: athena.plugins.php.set_php_version <version>
# RETURN: --
function athena.plugins.php.set_php_version()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "version"

	local version="$1"

	if ! athena.plugins.php.is_php_version_available "$version"; then
		athena.os.exit_with_msg "php${version} is not one of the supported versions ($(athena.plugins.php.get_supported_php_versions))."
		return 1
	fi

	athena.color.print_debug "Using php${version} container.."
	athena.plugin.use_container "php${version}"
	athena.plugin.set_image_name "$(athena.plugin.get_image_name)-php${version}"
	athena.plugin.set_container_name "$(athena.plugin.get_container_name)-php${version}"
	return 0
}

# Disable debugger inside the container
# USAGE: athena.plugins.php.disable_debugger_in_container
# RETURN: --
function athena.plugins.php.disable_debugger_in_container()
{
	athena.docker.add_env "ATHENA_DISABLE_DEBUG" "1"
}

# Enable profiler inside the container
# USAGE: athena.plugins.php.enable_profiler_in_container
# RETURN: --
function athena.plugins.php.enable_profiler_in_container()
{
	athena.docker.add_env "ATHENA_ENABLE_PROFILER" "1"
}

# USAGE: athena.plugins.php.add_link_to_docker <type> <link_name>
function athena.plugins.php.add_link_to_docker()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "type"
	athena.argument.argument_is_not_empty_or_fail "$2" "link_name"
	local type="$1"
	local link_name="$2"

	if athena.argument.argument_exists_and_remove "--skip-${type}"; then
		athena.color.print_info "Skipping auto link with ${type}..."
		return 1
	fi

	container_name=
	if athena.argument.argument_exists "--link-${type}"; then
		athena.argument.get_argument_and_remove "--link-${type}" "container_name"

		if ! athena.docker.is_container_running "$container_name"; then
			athena.os.exit_with_msg "Failed to auto link with ${type} '${container_name}'. Container is not running.."
		fi
	else
		if [[ "$type" == "proxy" ]]; then
			athena.plugin.require "proxy" "0.3.1"
			container_name=$(athena.plugins.proxy.get_container_name)
		else
			athena.plugin.require "selenium" "0.3.1"
			container_name="$(athena.plugins.selenium.get_container_name $type)"
		fi

		if ! athena.docker.is_container_running "$container_name"; then
			athena.color.print_debug "Skipped auto link with ${type} '${container_name}'. Container is not running."
			return 1
		fi
	fi

	athena.color.print_info "Auto linking with $type container '${container_name}'..."
	athena.docker.add_option --link "${container_name}:${link_name}"
}
