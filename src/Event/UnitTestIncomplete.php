<?php
namespace Athena\Event;

use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_Warning;
use Symfony\Component\EventDispatcher\Event;

class UnitTestIncomplete extends Event
{
    const ERROR = 'unit.test.error';
    const FAILURE = 'unit.test.failure';
    const SKIPPED = 'unit.test.skipped';
    const WARNING = 'unit.test.warning';
    const INCOMPLETE = 'unit.test.incomplete';
    const RISKY = 'unit.test.risky';
    /**
     * @var \PHPUnit_Framework_Test
     */
    private $test;
    /**
     * @var \Exception|\PHPUnit_Framework_AssertionFailedError|\PHPUnit_Framework_Warning
     */
    private $exception;

    /**
     * @var int
     */
    private $executionTime;

    /**
     * @param \PHPUnit_Framework_Test                 $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param int                                     $time
     *
     * @return static
     */
    public static function failure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        return new static($test, $e, $time);
    }

    /**
     * @param \PHPUnit_Framework_Test    $test
     * @param \PHPUnit_Framework_Warning $e
     * @param                            $time
     *
     * @return static
     */
    public static function warning(PHPUnit_Framework_Test $test, PHPUnit_Framework_Warning $e, $time)
    {
        return new static($test, $e, $time);
    }

    /**
     * UnitTestIncomplete constructor.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $exception
     * @param int                     $executionTime
     */
    public function __construct(PHPUnit_Framework_Test $test, Exception $exception, $executionTime)
    {
        $this->test = $test;
        $this->exception = $exception;
        $this->executionTime = $executionTime;
    }

    /**
     * @return \PHPUnit_Framework_Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @return \Exception|\PHPUnit_Framework_AssertionFailedError|\PHPUnit_Framework_Warning
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return int
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }
}

