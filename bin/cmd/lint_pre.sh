CMD_DESCRIPTION="Check files for syntax errors."
athena.os.usage 1 "<target-directory> [<options>...]" "$(cat <<EOF
<target-directory>               ; Directory in which syntax testing will be executed
[--exclude=<files>]              ; Comma seperated list of files to exclude
[--php-version=<version>]        ; Switch between available PHP versions. E.g. --php-version=7.0
EOF
)"
tests_dir="$(athena.argument.arg 1)"
athena.pop_args 1

athena.dir_exists_or_fail "$tests_dir" "Expected '$tests_dir' to be an existing directory."

tests_dir_full_path="$(athena.fs.get_full_path "$tests_dir")"

athena.docker.add_option -v "$tests_dir_full_path:/opt/tests"
athena.argument.prepend_to_arguments /opt/tests
