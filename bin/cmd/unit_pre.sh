CMD_DESCRIPTION="Run unit tests."
athena.os.usage 1 "<tests-directory> [<options>...] [(<phpunit-options>|<paratest-options>)...]" "$(cat <<EOF
<tests-directory>                ; This directory will be mounted inside the docker container. PHPUnit will be executed inside this directory
[-f=<config-file>]               ; Athena config file, with proxy configurations, grid options, etc
[--no-athena-bootstrap]          ; bootstrap.php located inside PHP plugin root dir, is injected automatically through phpunit --boostrap option. If you set this option, it won\'t be.
[--parallel=<number>]            ; Specify the number of jobs to be ran in parallel. In case this options is specified, Paratest will be ran, instead of PHPUnit
[--php-version=<version>]        ; Switch between available PHP versions. E.g. --php-version=7.0
[--override-athena-dependencies] ; Override PHP plugin dependencies with the ones found inside the tests directory
[--restore-athena-dependencies]  ; Restore PHP plugin original dependencies
EOF
)"
tests_dir=$(athena.fs.get_full_path $(athena.arg 1))
athena.fs.dir_exists_or_fail "$tests_dir" "Expected '$tests_dir' to be an existing directory."
athena.pop_args 1

if ! athena.arg_exists "-f"; then
	athena.docker.add_option "-v $tests_dir:/opt/tests -v $tests_dir:/opt/report"
	athena.argument.prepend_to_arguments /opt/tests
	return 0
fi

config_file_path="$(athena.argument.get_argument -f)"
athena.fs.file_exists_or_fail "$config_file_path" "Expected '$config_file_path' to be an existing file."
athena.argument.remove_argument "-f"

tests_dir_full_path="$(athena.fs.get_full_path $tests_dir)"
config_dir_full_path="$(athena.fs.get_full_path $(dirname $config_file_path))"
config_filename="$(athena.fs._basename $config_file_path)"

athena.docker.add_option "-v $tests_dir_full_path:/opt/tests -v $config_dir_full_path:/opt/config -v $tests_dir_full_path:/opt/report"
athena.argument.prepend_to_arguments /opt/tests "-f=/opt/config/$config_filename"
