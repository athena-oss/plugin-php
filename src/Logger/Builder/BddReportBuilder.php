<?php
namespace Athena\Logger\Builder;

use Athena\Athena;
use Athena\Logger\Structure\LoggerStructureNode;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Testwork\Tester\Result\TestResult;
use OutOfBoundsException;

class BddReportBuilder
{
    /**
     * @var LoggerStructureNode
     */
    private $pointer;

    /**
     * @var array
     */
    private $statistics;

    /**
     * @var array
     */
    private $resultCodes = [
        TestResult::PASSED    => 'passed',
        TestResult::SKIPPED   => 'skipped',
        TestResult::PENDING   => 'pending',
        TestResult::FAILED    => 'failed',
        StepResult::UNDEFINED => 'undefined'
    ];

    /**
     * BddReportBuilder constructor.
     */
    public function __construct()
    {
        $this->pointer = new LoggerStructureNode();

        $this->resetStatistics('total');
        $this->resetStatistics('total.feature');
        $this->resetStatistics('current.feature');
    }

    /**
     * @param string $name
     * @param string $directory
     * @param null   $browser
     */
    public function startSuite($name, $directory, $browser = null)
    {
        $this->pointer = $this->pointer
                ->withAttribute('type', 'suite')
                ->withAttribute('title', $name)
                ->withAttribute('directory', $directory);

        if ($browser !== null) {
            $this->pointer = $this->pointer
                ->withAttribute('browser', $browser);
        }

        $this->resetStatistics('total');
        $this->resetStatistics('total.feature');
    }

    /**
     * @param $title
     * @param $description
     */
    public function startFeature($title, $description, $tags)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'feature')
                ->withAttribute('title', $title)
                ->withAttribute('description', $description)
                ->withAttribute('tags', $tags);

        $this->resetStatistics('current.feature');
    }

    /**
     * @param $title
     * @param $tags
     */
    public function startScenario($title, $tags)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'scenario')
                ->withAttribute('title', $title)
                ->withAttribute('tags', $tags);
    }

    /**
     * @param string $text
     */
    public function addOutline($text)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'outline')
                ->withAttribute('text', $text)
            ->end();
    }

    /**
     * @param string $text
     * @param array $valuesTables
     * @param string $resultCode
     * @param null $imageFileName
     * @param null $exceptionMessage
     * @param null $exceptionTrace
     * @param null $exceptionType
     * @param array $httpTransactionSteps
     */
    public function finishStep(
        $text,
        $valuesTables,
        $resultCode,
        $imageFileName = null,
        $exceptionMessage = null,
        $exceptionTrace = null,
        $exceptionType = null,
        $httpTransactionSteps = []
    ) {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'step')
                ->withAttribute('text', $text)
                ->withAttribute('tables', $valuesTables)
                ->withAttribute('status', $this->getResultCodeAsString($resultCode));

        if ($imageFileName !== null) {

            $this->pointer->withAttribute('screenshot_url', './' . basename($imageFileName));
        }

        if ($exceptionMessage !== null) {
            $this->pointer
                ->withAttribute('exception_message', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
                ->withAttribute('exception_type', $exceptionType);
        }

        if ($httpTransactionSteps) {
            foreach($httpTransactionSteps as $httpTransaction) {
                $this->finishHttpTransaction(
                    $httpTransaction['request_method'],
                    $httpTransaction['request_url'],
                    $httpTransaction['request'],
                    $httpTransaction['response']
                );
            }
        }

        $this->pointer = $this->pointer->end();
    }

    /**
     * @param string $requestMethod
     * @param string $requestUrl
     * @param string $request
     * @param string $response
     */
    public function finishHttpTransaction($requestMethod, $requestUrl, $request, $response)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'http_transaction')
                ->withAttribute('request_method', $requestMethod)
                ->withAttribute('request_url', $requestUrl)
                ->withAttribute('request', $request)
                ->withAttribute('response', $response)
            ->end();
    }

    /**
     * @param string $resultCode
     * @param null   $trafficLoggerFile
     */
    public function finishScenario($resultCode, $trafficLoggerFile = null, $totalExecTime = null)
    {
        $statusText = $this->getResultCodeAsString($resultCode);

        // increase statistics
        $this->statistics['total'][$resultCode]++;
        $this->statistics['current.feature'][$resultCode]++;

        $this->pointer
            ->withAttribute('status', $statusText);

        if ($trafficLoggerFile) {
            $this->pointer
                ->withAttribute('traffic_logger_file', './'.basename($trafficLoggerFile));
        }

        if ($totalExecTime) {
            $this->pointer
                ->withAttribute('total_time', $totalExecTime);
        }

        $this->pointer = $this->pointer->end();
    }

    /**
     * @return void
     */
    public function finishFeature()
    {
        $isFeatureSuccessful = (boolean)($this->statistics['current.feature'][TestResult::FAILED] === 0);

        if (!$isFeatureSuccessful) {
            $this->statistics['total.feature'][TestResult::FAILED]++;
        } elseif ($isFeatureSuccessful) {
            $this->statistics['total.feature'][TestResult::PASSED]++;
        }

        $this->pointer = $this->pointer
            ->withAttribute('successful', $isFeatureSuccessful)
            ->end();
    }

    /**
     * @param float $totalExecTime
     */
    public function finishSuite($totalExecTime)
    {
        $totalTests = array_sum($this->statistics['total']);

        $passedPercentage = 0;
        $failurePercentage = 0;

        if ($totalTests > 0) {
            $passedPercentage = ceil(($this->statistics['total'][TestResult::PASSED] / $totalTests) * 100);
            $failurePercentage = 100 - $passedPercentage;
        }

        $this->pointer = $this->pointer
                ->withAttribute('successful', $this->statistics['total'][TestResult::FAILED] === 0)
                ->withAttribute('failures', $this->statistics['total'][TestResult::FAILED])
                ->withAttribute('skipped', $this->statistics['total'][TestResult::SKIPPED])
                ->withAttribute('errors', $this->statistics['total'][TestResult::PENDING])
                ->withAttribute('passed', $this->statistics['total'][TestResult::PASSED])
                ->withAttribute('features_passed', $this->statistics['total.feature'][TestResult::PASSED])
                ->withAttribute('features_failed', $this->statistics['total.feature'][TestResult::FAILED])
                ->withAttribute('total', $totalTests)
                ->withAttribute('failures_percentage', $failurePercentage)
                ->withAttribute('passed_percentage', $passedPercentage)
                ->withAttribute('total_time', $totalExecTime)
                ->withAttribute('directory', Athena::getInstance()->getTestsDirectory());
    }

    /**
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function build()
    {
        return $this->pointer;
    }

    private function resetStatistics($type)
    {
        $this->statistics[$type] = [
            TestResult::PASSED    => 0,
            TestResult::SKIPPED   => 0,
            TestResult::FAILED    => 0,
            TestResult::PENDING   => 0,
            StepResult::UNDEFINED => 0
        ];
    }

    /**
     * @param int $resultCode
     *
     * @return string
     */
    private function getResultCodeAsString($resultCode)
    {
        if (!array_key_exists($resultCode, $this->resultCodes)) {
            throw new OutOfBoundsException(sprintf("%d is not a valid BDD test result code.", $resultCode));
        }

        return $this->resultCodes[$resultCode];
    }
}

