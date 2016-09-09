<?php
namespace Athena\Logger;

use Athena\Configuration\Settings;
use Athena\Logger\Interpreter\InterpreterFactory;
use Athena\Stream\FileOutputStream;
use Athena\Stream\JsonInputStream;
use Athena\Stream\MergedTestResultsInputStream;
use Athena\Stream\StdinInputStream;

class LoggerFactory
{
    /**
     * @param \Athena\Configuration\Settings $settings
     *
     * @return \Athena\Logger\LoggerInterface
     */
    public static function fromSettings(Settings $settings)
    {
        if ($settings->exists('report')) {
            $outputDirectory = $settings->getByPath('report.outputDirectory')->orFail();
            $totalExecTime   = $settings->getByPath('athena_tests_exec_timer')->orFail();

            return (new LoggerBuilder())
                ->readWith(new MergedTestResultsInputStream(new JsonInputStream(new StdinInputStream()), $totalExecTime))
                ->parseWith(InterpreterFactory::fromSettings($settings))
                ->printWith(new FileOutputStream(sprintf("%s/report.html", $outputDirectory)))->build();
        }

        return new NoLogLogger();
    }
}

