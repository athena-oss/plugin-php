<?php
namespace Athena\Event;

use PHPUnit_Framework_TestSuite;
use Symfony\Component\EventDispatcher\Event;

class UnitSuiteCompleted extends Event
{
    const BEFORE = 'unit.suite.start';
    const AFTER  = 'unit.suite.end';

    /**
     * @var \PHPUnit_Framework_TestSuite
     */
    private $testSuite;

    /**
     * UnitTestSuiteCompleted constructor.
     *
     * @param \PHPUnit_Framework_TestSuite $testSuite
     */
    public function __construct(PHPUnit_Framework_TestSuite $testSuite)
    {
        $this->testSuite = $testSuite;
    }

    /**
     * @return \PHPUnit_Framework_TestSuite
     */
    public function getTestSuite()
    {
        return $this->testSuite;
    }
}

