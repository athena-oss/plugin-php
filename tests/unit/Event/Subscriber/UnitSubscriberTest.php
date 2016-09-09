<?php

namespace Athena\Tests\Event\Subscriber;

use Athena\Event\Subscriber\UnitSubscriber;
use Athena\Event\UnitSuiteCompleted;
use Athena\Event\UnitTestCompleted;
use Athena\Event\UnitTestIncomplete;
use Athena\Logger\Builder\UnitReportBuilder;
use Athena\Logger\PurgeStrategyInterface;
use Athena\Logger\Interpreter\InterpreterInterface;
use Athena\Stream\OutputStreamInterface;
use Phake;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_TestSuite;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UnitSubscriberTest extends PHPUnit_Framework_TestCase
{
    private $fakeInterpreter;
    private $actualUnitSubscriber;
    private $actualPhpUnitDispatcher;
    private $fakeOutputStream;

    protected function setUp()
    {
        $this->fakeInterpreter      = Phake::mock(InterpreterInterface::class);
        $this->fakeOutputStream         = Phake::mock(OutputStreamInterface::class);
        $this->actualUnitSubscriber = new UnitSubscriber($this->fakeInterpreter, $this->fakeOutputStream);

        $this->actualPhpUnitDispatcher = new EventDispatcher();
        $this->actualPhpUnitDispatcher->addSubscriber($this->actualUnitSubscriber);
    }

    /**
     * @param string $eventName
     * @param object $event
     */
    private function triggerUnitEvent($eventName, $event)
    {
        $this->actualPhpUnitDispatcher->dispatch($eventName, $event);
    }

    private function captureReportBuild()
    {
        $actualReport = null;

        $this->actualUnitSubscriber->__destruct();

        Phake::verify($this->fakeInterpreter)->interpret(Phake::capture($actualReport));

        return $actualReport;
    }

    public function testStartTestSuite_UnitFrameworkHasStartedTestSuite_ShouldBuildStartTestSuiteNode()
    {
        $fakePhpUnitTestSuite   = Phake::mock(PHPUnit_Framework_TestSuite::class);
        $fakeUnitSuiteCompleted = Phake::mock(UnitSuiteCompleted::class);

        Phake::when($fakePhpUnitTestSuite)->getName()->thenReturn("I'm a fake test suite.");
        Phake::when($fakeUnitSuiteCompleted)->getTestSuite()->thenReturn($fakePhpUnitTestSuite);

        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->startTestSuite("I'm a fake test suite.");

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testStartTestSuite_UnitFrameworkHasStartedTestSuiteWithoutName_ShouldDoNothing()
    {
        $fakePhpUnitTestSuite   = Phake::mock(PHPUnit_Framework_TestSuite::class);
        $fakeUnitSuiteCompleted = Phake::mock(UnitSuiteCompleted::class);

        Phake::when($fakePhpUnitTestSuite)->getName()->thenReturn(null);
        Phake::when($fakeUnitSuiteCompleted)->getTestSuite()->thenReturn($fakePhpUnitTestSuite);

        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);

        $expectedReportStructure = new UnitReportBuilder();

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testStartTestSuite_UnitFrameworkHasStartedTwoSuites_ShouldBuildOneParentAndOneChildNode()
    {
        $fakePhpUnitTestSuite   = Phake::mock(PHPUnit_Framework_TestSuite::class);
        $fakeUnitSuiteCompleted = Phake::mock(UnitSuiteCompleted::class);

        Phake::when($fakePhpUnitTestSuite)->getName()->thenReturn("I'm a fake test suite.");
        Phake::when($fakeUnitSuiteCompleted)->getTestSuite()->thenReturn($fakePhpUnitTestSuite);

        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);
        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->startTestSuite("I'm a fake test suite.");
        $expectedReportStructure->startChildTestSuite("I'm a fake test suite.");

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testStartTest_UnitFrameworkHasStartedTest_ShouldBuildStartTestNode()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_TestCase::class);
        $fakeUnitTestCompleted = Phake::mock(UnitTestCompleted::class);

        Phake::when($fakePhpUnitTest)->getName()->thenReturn("I'm a fake test.");
        Phake::when($fakeUnitTestCompleted)->getTest()->thenReturn($fakePhpUnitTest);

        $this->triggerUnitEvent(UnitTestCompleted::BEFORE, $fakeUnitTestCompleted);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->startTest("I'm a fake test.");

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddError_UnitFrameworkHasCaughtError_ShouldBuildErrorNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::ERROR, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addError(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddFailure_UnitFrameworkHasCaughtFailure_ShouldBuildFailureNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::FAILURE, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addFailure(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddRisky_UnitFrameworkHasCaughtRiskyTest_ShouldBuildRiskyNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::RISKY, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addRisky(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddSkipped_UnitFrameworkHasSkippedOneTest_ShouldBuildSkippedNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::SKIPPED, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addSkipped(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddWarning_UnitFrameworkHasCaughtWarning_ShouldBuildWarningdNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::WARNING, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addWarning(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testAddIncomplete_UnitFrameworkHasIncompleteTest_ShouldBuildIncompleteNode()
    {
        $fakeException          = new Exception('Fake exception.');
        $fakeUnitTestIncomplete = Phake::mock(UnitTestIncomplete::class);

        Phake::when($fakeUnitTestIncomplete)->getException()->thenReturn($fakeException);

        $this->triggerUnitEvent(UnitTestIncomplete::INCOMPLETE, $fakeUnitTestIncomplete);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->addIncomplete(get_class($fakeException), $fakeException->getMessage(), $fakeException->getTraceAsString());

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testEndTest_UnitFrameworkHasFinishedTest_ShouldBuildEndTestNode()
    {
        $fakePhpUnitTest       = Phake::mock(PHPUnit_Framework_TestCase::class);
        $fakeUnitTestCompleted = Phake::mock(UnitTestCompleted::class);

        Phake::when($fakePhpUnitTest)->getName()->thenReturn("I'm a fake test.");
        Phake::when($fakeUnitTestCompleted)->getExecutionTime()->thenReturn(10);
        Phake::when($fakeUnitTestCompleted)->getTest()->thenReturn($fakePhpUnitTest);

        $this->triggerUnitEvent(UnitTestCompleted::BEFORE, $fakeUnitTestCompleted);
        $this->triggerUnitEvent(UnitTestCompleted::AFTER, $fakeUnitTestCompleted);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->startTest("I'm a fake test.");
        $expectedReportStructure->endTest(10);

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testEndTestSuite_UnitFrameworkHasFinishedTestSuite_ShouldBuildEndTestSuiteNode()
    {
        $fakePhpUnitTestSuite   = Phake::mock(PHPUnit_Framework_TestSuite::class);
        $fakeUnitSuiteCompleted = Phake::mock(UnitSuiteCompleted::class);

        Phake::when($fakePhpUnitTestSuite)->getName()->thenReturn("I'm a fake test suite.");
        Phake::when($fakeUnitSuiteCompleted)->getTestSuite()->thenReturn($fakePhpUnitTestSuite);

        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);
        $this->triggerUnitEvent(UnitSuiteCompleted::AFTER, $fakeUnitSuiteCompleted);

        $expectedReportStructure = new UnitReportBuilder();
        $expectedReportStructure->startTestSuite("I'm a fake test suite.");
        $expectedReportStructure->endTestSuite();

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }

    public function testEndTestSuite_UnitFrameworkHasFinishedTestSuiteWithoutName_ShouldDoNothing()
    {
        $fakePhpUnitTestSuite   = Phake::mock(PHPUnit_Framework_TestSuite::class);
        $fakeUnitSuiteCompleted = Phake::mock(UnitSuiteCompleted::class);

        Phake::when($fakePhpUnitTestSuite)->getName()->thenReturn(null);
        Phake::when($fakeUnitSuiteCompleted)->getTestSuite()->thenReturn($fakePhpUnitTestSuite);

        $this->triggerUnitEvent(UnitSuiteCompleted::BEFORE, $fakeUnitSuiteCompleted);
        $this->triggerUnitEvent(UnitSuiteCompleted::AFTER, $fakeUnitSuiteCompleted);

        $expectedReportStructure = new UnitReportBuilder();

        $this->assertEquals($expectedReportStructure->build()->toArray(), $this->captureReportBuild());
    }
}
