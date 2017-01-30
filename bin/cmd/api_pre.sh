CMD_DESCRIPTION="Run api tests."
athena.os.usage 2 "<tests-directory> <config-file> [<options>...] [(<phpunit-options>|<paratest-options>)...]" "$(cat <<EOF
	<tests-directory>              ; This directory will be mounted inside the docker container. PHPUnit will be executed inside this directory
	<config-file>                  ; Athena config file, with proxy configurations, grid options, etc
	--parallel=<number>            ; Specify the number of jobs to be ran in parallel. In case this options is specified, Paratest will be ran, instead of PHPUnit
	--php-version=<version>        ; Switch between available PHP versions. E.g. --php-version=7.0
	--override-athena-dependencies ; Override PHP plugin dependencies with the ones found inside the tests directory
	--restore-athena-dependencies  ; Restore PHP plugin original dependencies
EOF
)"
tests_dir="$(athena.argument.arg 1)"
config_file_path="$(athena.argument.arg 2)"

athena.fs.dir_exists_or_fail "$tests_dir" "Expected '$tests_dir' to be an existing directory."
athena.fs.file_exists_or_fail "$config_file_path" "Expected '$config_file_path' to be an existing file."

config_filename="$(athena.fs._basename "$config_file_path")"
tests_dir_full_path="$(athena.fs.get_full_path "$tests_dir")"
config_dir_full_path="$(athena.fs.get_full_path "$(dirname "$config_file_path")")"

athena.docker.add_option -v "$tests_dir_full_path:/opt/tests" -v "$config_dir_full_path:/opt/config" -v "$tests_dir_full_path:/opt/report"

athena.argument.pop_arguments 2
athena.argument.prepend_to_arguments /opt/tests "/opt/config/$config_filename"
athena.plugins.php.add_link_to_docker "proxy" "athena-proxy"
