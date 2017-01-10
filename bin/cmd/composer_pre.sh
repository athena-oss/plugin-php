CMD_DESCRIPTION="Run Composer."
athena.os.usage 1 "<project-directory> [<options>...] [<composer-options>]" "$(cat <<EOF
<project-directory>              ; This directory will be mounted inside the docker container. Composer will be executed inside this directory
[--cache-dir=<dir>]              ; Directory where Composer cached packages should be stored
EOF
)"

project_dir="$(athena.path 1)"
athena.pop_args 1
athena.docker.mount_dir "$project_dir" /app

if athena.argument.argument_exists_and_remove "--cache-dir" "cache_dir"; then
    athena.docker.mount_dir "$cache_dir" "/composer"
else
    athena.docker.mount_dir "$HOME/.composer" "/composer"
fi

athena.plugin.use_container "composer/composer:php5"
athena.docker.set_no_default_router 
