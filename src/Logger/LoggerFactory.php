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
        if ($settings->isReportAvailable()) {
            $format = $settings->getByPath('report.format')->orDefaultTo('html');
            $outputDirectory = $settings->getByPath('report.outputDirectory')->orFail();
            $reportName = $settings->getByPath('report.name')->orDefaultTo('report');
            $totalExecTime   = $settings->getByPath('athena_tests_exec_timer')->orFail();

            return (new LoggerBuilder())
                ->readWith(new MergedTestResultsInputStream(new JsonInputStream(new StdinInputStream()), $totalExecTime))
                ->parseWith(InterpreterFactory::fromSettings($settings))
                ->printWith(new FileOutputStream(sprintf("%s/$reportName.$format", $outputDirectory)))->build();
        }

        return new NoLogLogger();
    }
}

