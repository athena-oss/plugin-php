CMD_DESCRIPTION="Analyse code smells against a custom or existing rule-set."
athena.os.usage 1 "<directory|file> [<options>...] [<phpcs-options>...]" "$(cat <<EOF
<directory|file>                 ; Directory or File to be tested
[--php-version=<version>]        ; Switch between available PHP versions. E.g. --php-version=7.0
EOF
)"

tests_dir="$(athena.arg 1)"
tests_dir_full_path="$(athena.fs.get_full_path "$tests_dir")"
tests_dir_absolute_path="$(athena.fs.absolutepath "$tests_dir")"

athena.pop_args 1

if [[ -f "$tests_dir" ]]; then
	target="$(echo "$tests_dir_absolute_path" | sed "s#$tests_dir_full_path#/opt/tests#g")"
else
	target="/opt/tests"
fi

athena.docker.add_option -v "$tests_dir_full_path:/opt/tests"
athena.argument.prepend_to_arguments "$target"
