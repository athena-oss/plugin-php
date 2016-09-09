athena.os.include_once "$LIB_DIR/shared/functions.sh"

function athena.plugins.php._behat()
{
	$BEHAT_CMD $@
	return $?
}

# Create basic structure for behat
# USAGE: athena.plugins.php._behat_init <tests_dir>
# RETURN: 0 (success) or 1 (failure)
function athena.plugins.php._behat_init()
{
	local tests_dir="$1"
	local exit_code=0

	pushd "$tests_dir" 1>/dev/null
	athena.plugins.php._behat --init
	exit_code=$?
	popd 1>/dev/null
	return $exit_code
}

# Search a given directory and execute all feature files in parallel with given options
# USAGE: athena.plugins.php._search_and_exec_features_in_parallel <search_dir> <features_nr> <behat_opts>
# RETURN: --
function athena.plugins.php._search_and_exec_features_in_parallel()
{
	local search_dir="$1"
	local features_nr="$2"
	local behat_opts="${@:3}"

        find "$search_dir" -type f -name "*.feature" -not -path "$search_dir/vendor/*" -print |\
                SHELL=$SHELL \
                parallel \
                --no-run-if-empty \
                --trim rl \
                --verbose \
		--keep-order \
                -j$parallel_features_nr \
                $BEHAT_CMD $behat_opts -- {}

	return $?
}

# Create a named pipe in given location
# USAGE: athena.plugins.php._create_pipe <pipe_path>
# RETURN: --
function athena.plugins.php._create_pipe()
{
	local pipe_path="$1"

	if [[ -f "$pipe_path" ]]; then
		$ATHENA_SUDO rm -f "$pipe_path"
	fi

	$ATHENA_SUDO exec 3<> "$pipe_path"
	$ATHENA_SUDO chmod a+rw "$pipe_path"
}

# Pipe named pipe contents to PHP aggregator script
# USAGE: athena.plugins.php._aggregate_pipe_output <pipe_path>
# RETURN: --
function athena.plugins.php._aggregate_pipe_output()
{
	local pipe_path="$1"
	cat "$pipe_path" | php "$WORKDIR/bin/aggregate.php"
}

# Trap exit signal and remove the named pipe. This avoids leaving named pipes around.
# USAGE: athena.plugins.php._register_pipe_cleanup_handler <pipe_name>
# RETURN: --
function athena.plugins.php._register_pipe_cleanup_handler()
{
	local pipe_name="$1"

	trap "$ATHENA_SUDO rm -f $pipe_name" EXIT
}

# Retrieve the PHP version running in the container.
# USAGE: athena.plugins.php.get_php_version
# RETURN: 0 (True) or 1 (False)
function athena.plugins.php.get_php_version()
{
	if [[ -z "$ATHENA_PHP_VERSION" ]]; then
		athena.os.exit_with_msg "ATHENA_PHP_VERSION is empty. It should contain PHP version. E.g. 5.6"
		return 1
	fi

	echo "$ATHENA_PHP_VERSION"
	return 0
}

# Enables, if possible, a PHP extension.
# USAGE: athena.plugins.php.enable_php_extension <extension_name>
# RETURN: 0 (True) or 1 (False). Outputs an error in case it fails.
function athena.plugins.php.enable_php_extension()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "Missing PHP extension name"

	local extension_name="$1"
	local php_version="$(athena.plugins.php.get_php_version)"

	athena.color.print_debug "Enabling PHP${php_version} extension ${extension_name}"
	athena.os.enable_quiet_mode
	case "$php_version" in
		"5."*) athena.os.exec php5enmod -s ALL "$extension_name"; return $? ;;
		"7.0") athena.os.exec docker-php-ext-enable "$extension_name"; return $? ;;
	esac

	athena.os.exit_with_msg "Cannot enable extension for php${php_version} as it's not supportted."
}

# Disables, if possible, a PHP extension.
# USAGE: athena.plugins.php.disable_php_extension <extension_name>
# RETURN: 0 (True) or 1 (False). Outputs an error in case it fails.
function athena.plugins.php.disable_php_extension()
{
	athena.argument.argument_is_not_empty_or_fail "$1" "Missing PHP extension name"

	local extension_name="$1"
	local php_version="$(athena.plugins.php.get_php_version)"

	athena.color.print_debug "Disabling PHP${php_version} extension ${extension_name}"
	athena.os.enable_quiet_mode
	case "$php_version" in
		"5."*) athena.os.exec php5dismod -s ALL "$extension_name"; return $? ;;
		"7.0") athena.os.exec docker-php-ext-disable "$extension_name"; return $? ;;
	esac

	athena.os.exit_with_msg "Cannot disable extension for php${php_version} as it's not supportted."
}

function athena.plugins.php._purge_reports()
{
	php "$WORKDIR/bin/purge_reports.php"
	return $?
}

function athena.plugins.php._init_proxy()
{
	php "$WORKDIR/bin/init_proxy.php"
	return $?
}

function athena.plugins.php.get_phpunit_cmd()
{
	if [ -f $PHPUNIT_TESTS_CMD ]; then
		echo $PHPUNIT_TESTS_CMD
	else
		echo $PHPUNIT_CMD
	fi
}

function athena.plugins.php.remove_composer_dependency()
{
	athena.color.print_info "removing dependency '$1' in $PWD"
	composer remove "$1" --no-interaction -o -q
	if [ -d $PWD/vendor/$1 ]; then
		athena.color.print_info "deleting vendor/$1"
		rm -rf $PWD/vendor/$1
	fi
}

function athena.plugins.php.require_composer_dependency()
{
	athena.color.print_info "including dependency '$1' in $PWD"
	composer require "$1" --no-interaction -o
}

function athena.plugins.php.override_composer_dependencies()
{
	if [ -f $OVERRIDE_LOCK_FILE ]; then
		athena.color.print_info "using overrided dependencies in project folder"
		if [ -f $PHPUNIT_TESTS_CMD ]; then
			PHPUNIT_CMD=$PHPUNIT_TESTS_CMD
		fi
		return
	fi

	athena_dependencies=$(cd $WORKDIR;composer show | awk '{ print $1":"$2}')
	project_dependencies=$(cd /opt/tests;composer show --direct| awk '{ print $1":"$2}')

	# removing athena overridable dependencies
	cp $WORKDIR/composer.json $WORKDIR/composer.json.bck
	cd $WORKDIR
	echo $project_dependencies | while read dep
	do
		name=$(echo $dep | awk -F':' '{ print $1 }')
		if [[ "$athena_dependencies" =~ .*"$name".* ]]; then
			athena.plugins.php.remove_composer_dependency $name
			echo
		fi
	done
	composer dumpautoload -o

	# updating project dependencies
	cd /opt/tests
	echo $project_dependencies | while read dep
	do
		athena.plugins.php.require_composer_dependency $dep
	done
	composer dumpautoload -o

	PHPUNIT_CMD=$(athena.plugins.php.get_phpunit_cmd)
	if [ ! -f $PHPUNIT_CMD ]; then
		athena.color.print_error "PHPUNIT not found."
		exit 1
	fi

	# all ok
	touch $OVERRIDE_LOCK_FILE
}

function athena.plugins.php.restore_default_composer_dependencies()
{
	if [ -f $WORKDIR/composer.json.bck ]; then
		athena.color.print_info "restoring athena default dependencies"
		mv $WORKDIR/composer.json.bck $WORKDIR/composer.json
		cd $WORKDIR
		composer update --prefer-dist --no-interaction -o

		if [ -f $OVERRIDE_LOCK_FILE ]; then
			cd /opt/tests
			composer update --prefer-dist --no-interaction -o
			rm $OVERRIDE_LOCK_FILE
		fi
	fi
}
