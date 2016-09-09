tests_dir="$(athena.arg 1)"

php_exit_code=0
pushd "$tests_dir" 1>/dev/null
if ! athena.argument.argument_exists '--exclude'; then
	find . -name "*.php" | SHELL=$SHELL parallel php -l >/dev/null
else
	list_arg=$(athena.argument.arg --exclude)
	list=$(echo $list_arg | sed "s#,#\|#g")
	# the 'SHELL=$SHELL' is necessary to avoid a 'parallel' warning
	# but it should not happen and might be a bug in the shell or
	# in perl or in parallel itself
	find . -name "*.php" | egrep -v -e "$list" | SHELL=$SHELL parallel php -l {} >/dev/null
	php_exit_code=$?
fi
popd 1>/dev/null

if [[ $php_exit_code -ne 0 ]]; then
	athena.os.exit $php_exit_code
fi

athena.color.print_ok "No errors were found..."
