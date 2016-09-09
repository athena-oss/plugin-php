<?php
/**
 * Entry point to purge old report files in a multi-process environment.
 */

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

if (($pipe = getenv('ATHENA_REPORT_PIPE_NAME')) !== false) {
    define('ATHENA_REPORT_PIPE_NAME', $pipe);
}

require __DIR__ . '/../vendor/autoload.php';

use Athena\Athena;
use Athena\Logger\PurgeStrategyFactory;

PurgeStrategyFactory::fromSettings(Athena::settings())->purge();
