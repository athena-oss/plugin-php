<?php

// Shields Athena against unexpected behaviour
// This file must only be included once
if (defined('INCLUDED')) {
    return;
}

define('INCLUDED', true);
define('ATHENA_CONFIGURATION_FILE', getenv('ATHENA_CONFIGURATION_FILE'));
define('ATHENA_TESTS_DIRECTORY', getenv('ATHENA_TESTS_DIRECTORY'));
define('ATHENA_TESTS_TYPE', getenv('ATHENA_TESTS_TYPE'));
define('ATHENA_START_TIMER', getenv('ATHENA_START_TIMER'));

if (($browser = getenv('ATHENA_BROWSER')) !== false) {
    define('ATHENA_BROWSER', $browser);
}

if (($pipe = getenv('ATHENA_REPORT_PIPE_NAME')) !== false) {
    define('ATHENA_REPORT_PIPE_NAME', $pipe);
}

/**
 * @var $autoloader \Composer\Autoload\ClassLoader
 */
$autoloader = require __DIR__ . "/vendor/autoload.php";
$autoloader->addPsr4('Tests\\', ATHENA_TESTS_DIRECTORY);

use Athena\Athena;
$athena     = Athena::getInstance();

// add additional namespaces autoloading - PS4 compliant
$extraNamespaces = $athena->settings()->get('psr4')->orDefaultTo([]);
if (!empty($extraNamespaces)) {
    foreach ($extraNamespaces as $name => $location) {
        $autoloader->addPsr4($name, $location);
    }
}

// add additional bootstrap files
$extraBootstrapFiles = $athena->settings()->get('bootstrap')->orDefaultTo([]);
if (!empty($extraBootstrapFiles)) {
    foreach ($extraBootstrapFiles as $filename) {
        $bootstrapFileName = ATHENA_TESTS_DIRECTORY . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($bootstrapFileName)) {
            trigger_error(sprintf('Failed to load custom bootstrap file "%s" into Athena. Check your Athena configuration file.', $bootstrapFileName), E_USER_ERROR);
            continue;
        }

        require_once $bootstrapFileName;
    }
}

$athena->registerSubscribers();

// try to cleanup unused browsers that can be still in use because of exception being thrown
register_shutdown_function(function () use ($athena) {
    if (in_array(ATHENA_TESTS_TYPE, ['browser', 'bdd']) && !is_null($athena->getBrowser())) {
        $athena->getBrowser()->cleanup();
    }
});
