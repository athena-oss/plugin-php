pushd "$WORKDIR" 1>/dev/null

json_hash=$(md5sum composer.json | awk '{ print $1 }')
hash=$([ -f $WORKDIR/composer.lock ] && grep "\"hash\"" $WORKDIR/composer.lock | awk -F':' '{ print $2}' | sed -e 's/.*"\(.*\)".*/\1/p' | head -1)

if [[ "$json_hash" == "$hash" ]]; then
	operation="install"
else
	operation="update"
fi

athena.color.print_info "Running composer ${operation}..."
if ! composer $operation --prefer-dist --no-interaction -o; then
	popd 1>/dev/null
	athena.fatal "Composer failed to complete $operation operation..."
fi

popd 1>/dev/null
