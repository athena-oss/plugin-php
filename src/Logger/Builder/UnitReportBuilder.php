<?php
namespace Athena\Logger\Builder;

use Athena\Logger\Structure\LoggerStructureNode;

class UnitReportBuilder
{
    /**
     * @var \Athena\Logger\Structure\LoggerStructureNode
     */
    private $pointer;

    /**
     * @var array
     */
    private $statistics = [
        'total_tests'      => 0,
        'total_errors'     => 0,
        'total_risky'      => 0,
        'total_failures'   => 0,
        'total_skipped'    => 0,
        'total_warning'    => 0,
        'total_incomplete' => 0,
        'total_passed'     => 0
    ];

    /**
     * @var bool
     */
    private $wasSuiteSuccessful = true;

    /**
     * @var bool
     */
    private $wasTestSuccessful = true;

    public function __construct()
    {
        $this->pointer = new LoggerStructureNode();
    }

    /**
     * @param string $title
     */
    public function startTestSuite($title)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'suite')
                ->withAttribute('title', $title);

        $this->statistics = [
            'total_tests'      => 0,
            'total_errors'     => 0,
            'total_risky'      => 0,
            'total_failures'   => 0,
            'total_skipped'    => 0,
            'total_warning'    => 0,
            'total_incomplete' => 0,
            'total_passed'     => 0
        ];

        $this->wasSuiteSuccessful = true;
    }

    /**
     * @param string $title
     */
    public function startChildTestSuite($title)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'child_suite')
                ->withAttribute('title', $title);
    }

    /**
     * @param string $name
     */
    public function startTest($name)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'test')
                ->withAttribute('title', $name);

        // reset test success
        $this->wasTestSuccessful = true;

        $this->statistics['total_tests']++;
    }

    /**
     * @param string $description
     * @param string $screenshotFile
     */
    public function addStep($description, $screenshotFile = null)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'step')
                ->withAttribute('description', $description);

        if ($screenshotFile !== null) {
            $this->pointer->withAttribute('screenshot_url', './' . basename($screenshotFile));
        }

        $this->pointer = $this->pointer->end();
    }

    /**
     * @param string $request
     * @param string $response
     *
     */
    public function addHttpTransaction($request, $response)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'transaction')
                ->withAttribute('request', $request)
                ->withAttribute('response', $response)
            ->end();
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addError($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'exception')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_errors']++;

        $this->wasTestSuccessful = false;
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addFailure($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'failure')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_failures']++;

        $this->wasTestSuccessful = false;
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addRisky($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'risky')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_risky']++;
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addSkipped($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'skipped')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_skipped']++;
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addWarning($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'warning')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_warning']++;
    }

    /**
     * @param string $exceptionType
     * @param string $exceptionMessage
     * @param string $exceptionTrace
     */
    public function addIncomplete($exceptionType, $exceptionMessage, $exceptionTrace)
    {
        $this->pointer = $this->pointer
            ->withChildren()
            ->newNode()
                ->withAttribute('type', 'warning')
                ->withAttribute('exception_type', $exceptionType)
                ->withAttribute('exception_msg', $exceptionMessage)
                ->withAttribute('exception_trace', $exceptionTrace)
            ->end();

        $this->statistics['total_incomplete']++;
    }

    /**
     * @param int  $executionTime
     * @param null $trafficLoggerFile
     */
    public function endTest($executionTime, $trafficLoggerFile = null)
    {
        if ($this->wasTestSuccessful) {
            $this->statistics['total_passed']++;
        }

        // in case some test fails, we consider suite not be successful
        if (!$this->wasTestSuccessful) {
            $this->wasSuiteSuccessful = false;
        }

        $this->pointer
                ->withAttribute('successful', $this->wasTestSuccessful)
                ->withAttribute('time', $executionTime);

        if ($trafficLoggerFile) {
            $this->pointer
                ->withAttribute('traffic_logger_file', './'.basename($trafficLoggerFile));
        }

        $this->pointer = $this->pointer->end();
    }

    public function endTestSuite()
    {
        // set statistics
        foreach ($this->statistics as $key => $value) {
            $this->pointer->withAttribute($key, $value);
        }

        $successPercent = 0;
        $failurePercent = 0;

        if ($this->statistics['total_tests'] > 0) {
            $successPercent = ceil(($this->statistics['total_passed'] / $this->statistics['total_tests']) * 100);
            $failurePercent = 100 - $successPercent;
        }

        $this->pointer = $this->pointer
                ->withAttribute('success_percentage', $successPercent)
                ->withAttribute('failure_percentage', $failurePercent)
                ->withAttribute('successful', $this->wasSuiteSuccessful)
            ->end();
    }

    /**
     * @return \Athena\Logger\Structure\LoggerStructureNode
     */
    public function build()
    {
        return $this->pointer;
    }
}

