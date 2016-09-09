<?php
namespace Athena\Event\Adapter;

use Athena\Event\Dispatcher\DispatcherLocator;
use Athena\Event\UnitSuiteCompleted;
use Athena\Event\UnitTestCompleted;
use Athena\Event\UnitTestIncomplete;
use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_Warning;

/**
 * Adapts events from PHP Unit to Athena.
 *
 * PHP Unit does not have one single point for event identification, such as an ENUM, so we can't really proxy
 * the events. We have to adapt them.
 *
 * @package Athena\Event\Adapter
 */
class PhpUnitAdapter implements PHPUnit_Framework_TestListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    /**
     * PhpUnitSubscriber constructor.
     *
     * @param \Athena\Event\Dispatcher\DispatcherLocator $dispatcherLocator
     */
    public function __construct(DispatcherLocator $dispatcherLocator)
    {
        $this->eventDispatcher = $dispatcherLocator->locate();
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->eventDispatcher->dispatch(UnitSuiteCompleted::BEFORE, new UnitSuiteCompleted($suite));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->eventDispatcher->dispatch(UnitTestCompleted::BEFORE, new UnitTestCompleted($test));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::ERROR, new UnitTestIncomplete($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test                 $test
     * @param \PHPUnit_Framework_AssertionFailedError $e
     * @param float                                   $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::FAILURE, UnitTestIncomplete::failure($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::SKIPPED, new UnitTestIncomplete($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test    $test
     * @param \PHPUnit_Framework_Warning $e
     * @param                            $time
     */
    public function addWarning(PHPUnit_Framework_Test $test, PHPUnit_Framework_Warning $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::WARNING, UnitTestIncomplete::warning($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::INCOMPLETE, new UnitTestIncomplete($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param \Exception              $e
     * @param float                   $time
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestIncomplete::RISKY, new UnitTestIncomplete($test, $e, $time));
    }

    /**
     * @param \PHPUnit_Framework_Test $test
     * @param float                   $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $this->eventDispatcher->dispatch(UnitTestCompleted::AFTER, new UnitTestCompleted($test, $time));
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->eventDispatcher->dispatch(UnitSuiteCompleted::AFTER, new UnitSuiteCompleted($suite));
    }
}

