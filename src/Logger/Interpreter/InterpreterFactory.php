<?php
namespace Athena\Logger\Interpreter;

use Athena\Configuration\Settings;
use Athena\Exception\InvalidLoggerParser;

class InterpreterFactory
{
    /**
     * @param \Athena\Configuration\Settings $settings
     *
     * @return \Athena\Logger\Interpreter\InterpreterInterface
     * @throws \Athena\Exception\SettingNotFoundException
     */
    public static function fromSettings(Settings $settings)
    {
        $format = $settings->getByPath('report.format')->orDefaultTo('html');
        
        $testsType = $settings->getByPath('athena_tests_type')->orFail();

        switch ($format) {
            case 'html':
                return new HtmlInterpreter(sprintf("%s_report.twig", $testsType));
            case 'json':
                return new HtmlInterpreter(sprintf("json_report.twig", $testsType));
            case 'cucumber':
                return new HtmlInterpreter(sprintf("cucumber_report.twig", $testsType));
            default:
                throw new InvalidLoggerParser(sprintf("%s is not a valid report format.", $format));
        }
    }
}

