CMD_DESCRIPTION="Removes vendor related stuff."

if [ -f $ATHENA_PLG_DIR/athena.lock ]; then
	if rm "$ATHENA_PLG_DIR/athena.lock"; then
		athena.color.print_ok "Removed $ATHENA_PLG_DIR/athena.lock"
	else
		athena.color.print_error "Failed to remove $ATHENA_PLG_DIR/athena.lock file..."
	fi
fi

if [ -d $ATHENA_PLG_DIR/vendor ]; then
	if rm -rf "$ATHENA_PLG_DIR/vendor"; then
		athena.color.print_ok "Removed $ATHENA_PLG_DIR/vendor"
	else
		athena.os.exit_with_msg "Failed to remove $ATHENA_PLG_DIR/vendor directory..."
	fi
fi

docker_images="$(docker images --format "{{.Repository}}:{{.Tag}}" | grep "^$(athena.plugin.get_tag_name)")"
if [[ -n "$docker_images" ]]; then
	for image_and_tag in $docker_images; do
		image_name="$(echo $image_and_tag | tr ':' ' ' | awk '{ print $1 }')"
		tag_name="$(echo $image_and_tag | tr ':' ' ' | awk '{ print $2 }')"

		if [[ -z $image_name || -z $tag_name ]]; then
			continue
		fi

		athena.docker.remove_container_and_image "$image_name" "$tag_name"
	done
fi

athena.color.print_info "Finished cleanup..."
