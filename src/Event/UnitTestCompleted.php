<?php
namespace Athena\Event;

use PHPUnit_Framework_Test;
use Symfony\Component\EventDispatcher\Event;

class UnitTestCompleted extends Event
{
    const BEFORE   = 'unit.test.start';
    const AFTER    = 'unit.test.end';

    /**
     * @var \PHPUnit_Framework_Test
     */
    private $test;
    /**
     * @var int
     */
    private $executionTime;

    /**
     * UnitTestCompleted constructor.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param int                     $executionTime
     */
    public function __construct(PHPUnit_Framework_Test $test, $executionTime = 0)
    {
        $this->test = $test;
        $this->executionTime = $executionTime;
    }

    /**
     * @return int
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * @return PHPUnit_Framework_Test
     */
    public function getTest()
    {
        return $this->test;
    }
}

