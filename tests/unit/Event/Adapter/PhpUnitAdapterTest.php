<?php

namespace Athena\Tests\Event\Adapter;

use Athena\Event\Adapter\PhpUnitAdapter;
use Athena\Event\Dispatcher\DispatcherLocator;
use Athena\Event\UnitSuiteCompleted;
use Athena\Event\UnitTestCompleted;
use Athena\Event\UnitTestIncomplete;
use Exception;
use Phake;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_Warning;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PhpUnitAdapterTest extends PHPUnit_Framework_TestCase
{
    private $actualPhpUnitAdapter;
    private $fakeEventDispatcher;

    protected function setUp()
    {
        $fakeEventDispatcher   = Phake::mock(EventDispatcher::class);
        $fakeDispatcherLocator = Phake::mock(DispatcherLocator::class);

        Phake::when($fakeDispatcherLocator)->locate()->thenReturn($fakeEventDispatcher);

        $this->actualPhpUnitAdapter = new PhpUnitAdapter($fakeDispatcherLocator);
        $this->fakeEventDispatcher  = $fakeEventDispatcher;
    }

    /**
     * @param string                                   $eventName
     * @param \Symfony\Component\EventDispatcher\Event $event
     */
    private function assertWasDispatched($eventName, $event)
    {
        Phake::verify($this->fakeEventDispatcher)->dispatch($eventName, $event);
    }

    public function testStartTestSuite_PhpUnitStartsSuite_ShouldDispatchUnitSuiteCompleteBeforeEvent()
    {
        $fakePhpUnitTestSuite  = Phake::mock(PHPUnit_Framework_TestSuite::class);

        $this->actualPhpUnitAdapter->startTestSuite($fakePhpUnitTestSuite);

        $this->assertWasDispatched(UnitSuiteCompleted::BEFORE, new UnitSuiteCompleted($fakePhpUnitTestSuite));
    }

    public function testStartTest_PhpUnitStartsTest_ShouldDispatchUnitTestCompletedBeforeEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);

        $this->actualPhpUnitAdapter->startTest($fakePhpUnitTest);

        $this->assertWasDispatched(UnitTestCompleted::BEFORE, new UnitTestCompleted($fakePhpUnitTest));
    }

    public function testAddError_PhpUnitCaughtAnErrorInTheTest_ShouldDispatchUnitTestIncompleteErrorEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeException         = Phake::mock(Exception::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addError($fakePhpUnitTest, $fakeException, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::ERROR, new UnitTestIncomplete($fakePhpUnitTest, $fakeException, $fakeTestTiming));
    }

    public function testAddFailure_PhpUnitCaughtAFailureInTheTest_ShouldDispatchUnitTestIncompleteFailureEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeAssertionFailure  = Phake::mock(PHPUnit_Framework_AssertionFailedError::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addFailure($fakePhpUnitTest, $fakeAssertionFailure, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::FAILURE, UnitTestIncomplete::failure($fakePhpUnitTest, $fakeAssertionFailure, $fakeTestTiming));
    }

    public function testAddSkippedTest_PhpUnitSkippedATest_ShouldDispatchUnitTestIncompleteSkippedEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeException         = Phake::mock(Exception::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addSkippedTest($fakePhpUnitTest, $fakeException, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::SKIPPED, new UnitTestIncomplete($fakePhpUnitTest, $fakeException, $fakeTestTiming));
    }

    public function testAddWarning_PhpUnitCaughtAWarningInATest_ShouldDispatchUnitTestIncompleteWarningEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakePhpUnitWarning    = Phake::mock(PHPUnit_Framework_Warning::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addWarning($fakePhpUnitTest, $fakePhpUnitWarning, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::WARNING, UnitTestIncomplete::warning($fakePhpUnitTest, $fakePhpUnitWarning, $fakeTestTiming));
    }

    public function testIncompleteTest_PhpUnitCaughtAIncompleteTest_ShouldDispatchUnitTestIncompleteEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeException         = Phake::mock(Exception::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addIncompleteTest($fakePhpUnitTest, $fakeException, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::INCOMPLETE, new UnitTestIncomplete($fakePhpUnitTest, $fakeException, $fakeTestTiming));
    }

    public function testRiskyTest_PhpUnitCaughtARiskyTest_ShouldDispatchUnitTestIncompleteRiskyEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeException         = Phake::mock(Exception::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->addRiskyTest($fakePhpUnitTest, $fakeException, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestIncomplete::RISKY, new UnitTestIncomplete($fakePhpUnitTest, $fakeException, $fakeTestTiming));
    }

    public function testEndTest_PhpUnitAsFinishedATest_ShouldDispatchUnitTestCompletedAfterEvent()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_Test::class);
        $fakeTestTiming        = 10;

        $this->actualPhpUnitAdapter->endTest($fakePhpUnitTest, $fakeTestTiming);

        $this->assertWasDispatched(UnitTestCompleted::AFTER, new UnitTestCompleted($fakePhpUnitTest, $fakeTestTiming));
    }

    public function testEndTestSuite_PhpUnitAsFinishedTheTestingSuite_ShouldDispatchUnitSuiteCompletedAfterEvent()
    {
        $fakePhpUnitTestSuite  = Phake::mock(PHPUnit_Framework_TestSuite::class);

        $this->actualPhpUnitAdapter->endTestSuite($fakePhpUnitTestSuite);

        $this->assertWasDispatched(UnitSuiteCompleted::AFTER, new UnitSuiteCompleted($fakePhpUnitTestSuite));
    }
}
