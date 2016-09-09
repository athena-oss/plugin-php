CMD_DESCRIPTION="Initialize plugin for the first time."

composer_dir="${ATHENA_COMPOSER_DIR:-"$HOME/.composer"}"

if [[ ! -d "$composer_dir" ]]; then
	if ! mkdir "$composer_dir"; then
		athena.fatal "Failed to created $composer_dir directory"
	fi
fi

athena.color.print_info "Mounting $composer_dir at /root/.composer"
athena.docker.add_option "-v $composer_dir:/root/.composer"
