CMD_DESCRIPTION="Run browser tests."
athena.os.usage 3 "<browser-name> <tests-directory> <config-file> [<options>...] [(<phpunit-options>|<paratest-options>)...]" "$(cat <<EOF
<browser-name>                   ; Browser to be used. Such as firefox, phantomjs, or chrome
<tests-directory>                ; This directory will be mounted inside the docker container, and used to search for the tests
<config-file>                    ; Athena config file, with proxy configurations, grid options, etc
[--parallel=<number>]            ; Specify the number of jobs to be ran in parallel. In case this options is specified, Paratest will be ran, instead of PHPUnit
[--php-version=<version>]        ; Switch between available PHP versions. E.g. --php-version=7.0
[--override-athena-dependencies] ; Override PHP plugin dependencies with the ones found inside the tests directory
[--restore-athena-dependencies]  ; Restore PHP plugin original dependencies
EOF
)"
browser_name="$(athena.argument.arg 1)"
tests_dir="$(athena.argument.arg 2)"
config_file_path="$(athena.argument.arg 3)"

athena.pop_args 3

athena.fs.dir_exists_or_fail "$tests_dir" "Expected '$tests_dir' to be an existing directory."
athena.fs.file_exists_or_fail "$config_file_path" "Expected '$config_file_path' to be an existing file."

config_filename="$(athena.fs._basename "$config_file_path")"
tests_dir_full_path="$(athena.fs.get_full_path "$tests_dir")"
config_dir_full_path="$(athena.fs.get_full_path "$(dirname "$config_file_path")")"

athena.docker.add_option -v "$tests_dir_full_path:/opt/tests" -v "$config_dir_full_path:/opt/config" -v "$tests_dir_full_path:/opt/report"

athena.argument.prepend_to_arguments "$browser_name" /opt/tests "/opt/config/$config_filename"

athena.plugins.php.add_link_to_docker "proxy" "athena-proxy"
athena.plugins.php.add_link_to_docker "hub" "athena-selenium-hub"
