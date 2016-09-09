<?php

namespace Athena\Tests\Event\Subscriber;

use Athena\Event\Subscriber\BddSubscriber;
use Athena\Logger\Builder\BddReportBuilder;
use Athena\Logger\Structure\LoggerStructureNode;
use Athena\Logger\Interpreter\InterpreterInterface;
use Athena\Logger\Timer\TimerInterface;
use Athena\Stream\OutputStreamInterface;
use Behat\Behat\Definition\SearchResult;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Call\Call;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Teardown;
use Exception;
use Phake;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BddSubscriberTest extends \PHPUnit_Framework_TestCase
{
    const EXECUTION_TIME = 1;

    private $fakeInterpreter;
    private $actualBddSubscriber;
    private $actualDispatcher;
    private $outputStream;
    private $timer;

    protected function setUp()
    {
        $this->fakeInterpreter     = Phake::mock(InterpreterInterface::class);
        $this->outputStream        = Phake::mock(OutputStreamInterface::class);
        $this->timer               = Phake::mock(TimerInterface::class);

        Phake::when($this->timer)->stop('suite')->thenReturn(static::EXECUTION_TIME);

        $this->actualBddSubscriber = new BddSubscriber($this->fakeInterpreter, $this->outputStream, $this->timer);

        $this->actualDispatcher = new EventDispatcher();
        $this->actualDispatcher->addSubscriber($this->actualBddSubscriber);
    }

    /**
     * @param string $eventName
     * @param object $event
     */
    private function triggerUnitEvent($eventName, $event)
    {
        $this->actualDispatcher->dispatch($eventName, $event);
    }

    private function captureReportBuild()
    {
        $actualReport = null;

        $this->actualBddSubscriber->__destruct();

        Phake::verify($this->fakeInterpreter)->interpret(Phake::capture($actualReport));

        return $actualReport;
    }

    public function testBeforeExercise_BddFrameworkStartedRunning_ShouldReturnEmptyReportNodeInstance()
    {
        $fakeSuite = Phake::mock(ExerciseCompleted::class);

        $this->triggerUnitEvent(ExerciseCompleted::BEFORE, $fakeSuite);

        $this->assertEquals([], $this->captureReportBuild());
    }

    public function testBeforeSuite_BddFrameworkStartedSuite_ShouldBuildSuiteNode()
    {
        $fakeSuite       = Phake::mock(Suite::class);
        $fakeSuiteTested = Phake::mock(SuiteTested::class);

        Phake::when($fakeSuite)->getName()->thenReturn("fake test suite.");
        Phake::when($fakeSuite)->getSetting('paths')->thenReturn([]);
        Phake::when($fakeSuiteTested)->getSuite()->thenReturn($fakeSuite);

        $this->triggerUnitEvent(SuiteTested::BEFORE, $fakeSuiteTested);

        $actualReport = $this->captureReportBuild();

        $this->assertEquals($actualReport['type'], "suite");
        $this->assertEquals($actualReport['title'], "fake test suite.");
    }

    public function testBeforeFeature_BddFrameworkStartedFeature_ShouldBuildFeatureNode()
    {
        $fakeFeature       = Phake::mock(FeatureNode::class);
        $fakeFeatureTested = Phake::mock(FeatureTested::class);

        Phake::when($fakeFeature)->getTitle()->thenReturn("fake title");
        Phake::when($fakeFeature)->getDescription()->thenReturn("fake description");
        Phake::when($fakeFeatureTested)->getFeature()->thenReturn($fakeFeature);

        $this->triggerUnitEvent(FeatureTested::BEFORE, $fakeFeatureTested);

        $actualReport = $this->captureReportBuild();

        $this->assertEquals($actualReport['type'], "feature");
        $this->assertEquals($actualReport['title'], "fake title");
        $this->assertEquals($actualReport['description'], "fake description");
    }

    public function testBeforeScenario_BddFrameworkStartedScenario_ShouldBuildScenarioNode()
    {
        $fakeScenario       = Phake::mock(ScenarioInterface::class);
        $fakeScenarioTested = Phake::mock(ScenarioTested::class);

        Phake::when($fakeScenario)->getTitle()->thenReturn("fake title");
        Phake::when($fakeScenario)->getTags()->thenReturn("fakeTag");
        Phake::when($fakeScenarioTested)->getScenario()->thenReturn($fakeScenario);

        $this->triggerUnitEvent(ScenarioTested::BEFORE, $fakeScenarioTested);

        $actualReport = $this->captureReportBuild();

        $this->assertEquals($actualReport['type'], "scenario");
        $this->assertEquals($actualReport['title'], "fake title");
    }

    public function testAfterStep_BddFrameworkFinishedStepSuccessfully_ShouldBuildStepNodeWithoutExceptions()
    {
        $fakeEnvironment = Phake::mock(Environment::class);
        $fakeFeatureNode = Phake::mock(FeatureNode::class);
        $fakeStepNode    = Phake::mock(StepNode::class);
        $fakeStepResult  = Phake::mock(StepResult::class);
        $fakeTeardown    = Phake::mock(Teardown::class);
        $fakeText        = 'FAKE fake text';

        Phake::when($fakeStepResult)->getResultCode()->thenReturn(TestResult::PASSED);
        Phake::when($fakeStepNode)->getKeyword()->thenReturn("FAKE");
        Phake::when($fakeStepNode)->getText()->thenReturn("fake text");

        $fakeAfterStepTested = new AfterStepTested($fakeEnvironment, $fakeFeatureNode, $fakeStepNode, $fakeStepResult, $fakeTeardown);

        $this->triggerUnitEvent(StepTested::AFTER, $fakeAfterStepTested);

        $actualReport = $this->captureReportBuild()['children'][0];

        $this->assertEquals($actualReport['type'], "step");
        $this->assertEquals($actualReport['text'], "FAKE fake text");
        $this->assertEquals($actualReport['tables'], []);
        $this->assertEquals($actualReport['status'], 'passed');
    }

    public function testAfterStep_BddFrameworkStepFailed_ShouldBuildStepNodeWithExceptionMessage()
    {
        $fakeCallResult   = Phake::mock(Call::class);
        $fakeSearchResult = new SearchResult();
        $fakeException    = new Exception('fake exception');
        $fakeCallResult   = new CallResult($fakeCallResult, TestResult::FAILED, $fakeException);
        $fakeStepResult   = new ExecutedStepResult($fakeSearchResult, $fakeCallResult);
        $fakeEnvironment  = Phake::mock(Environment::class);
        $fakeFeatureNode  = Phake::mock(FeatureNode::class);
        $fakeStepNode     = Phake::mock(StepNode::class);
        $fakeTeardown     = Phake::mock(Teardown::class);
        $fakeText         = 'FAKE fake text';

        Phake::when($fakeStepNode)->getKeyword()->thenReturn("FAKE");
        Phake::when($fakeStepNode)->getText()->thenReturn("fake text");

        $fakeAfterStepTested = new AfterStepTested($fakeEnvironment, $fakeFeatureNode, $fakeStepNode, $fakeStepResult, $fakeTeardown);

        $this->triggerUnitEvent(StepTested::AFTER, $fakeAfterStepTested);

        $expectedReport = new BddReportBuilder();
        $expectedReport->finishStep(
            $fakeText,
            [],
            TestResult::FAILED,
            null,
            $fakeException->getMessage(),
            $fakeException->getTraceAsString(),
            get_class($fakeException)
        );

        $this->assertEquals($expectedReport->build()->toArray(), $this->captureReportBuild());
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage 230230 is not a valid BDD test result code.
     */
    public function testAfterStep_BddFrameworkStepFailedWithNonExistantResultCode_ShouldBuildStepNodeWithExceptionMessage()
    {
        $fakeCallResult   = Phake::mock(Call::class);
        $fakeSearchResult = new SearchResult();
        $fakeException    = new Exception('fake exception');
        $fakeCallResult   = new CallResult($fakeCallResult, TestResult::FAILED, $fakeException);
        $fakeStepResult   = new ExecutedStepResult($fakeSearchResult, $fakeCallResult);
        $fakeEnvironment  = Phake::mock(Environment::class);
        $fakeFeatureNode  = Phake::mock(FeatureNode::class);
        $fakeStepNode     = Phake::mock(StepNode::class);
        $fakeTeardown     = Phake::mock(Teardown::class);
        $fakeText         = 'FAKE fake text';

        Phake::when($fakeStepNode)->getKeyword()->thenReturn("FAKE");
        Phake::when($fakeStepNode)->getText()->thenReturn("fake text");

        $fakeAfterStepTested = new AfterStepTested($fakeEnvironment, $fakeFeatureNode, $fakeStepNode, $fakeStepResult, $fakeTeardown);

        $this->triggerUnitEvent(StepTested::AFTER, $fakeAfterStepTested);

        $expectedReport = new BddReportBuilder();
        $expectedReport->finishStep(
            $fakeText,
            [],
            230230,
            null,
            $fakeException->getMessage(),
            $fakeException->getTraceAsString(),
            get_class($fakeException)
        );

        $this->assertEquals($expectedReport->build(), $this->captureReportBuild());
    }

    public function testAfterScenario_BddFrameworkFinishedScenario_ShouldBuildScenarioNode()
    {
        $fakeEnvironment    = Phake::mock(Environment::class);
        $fakeFeatureNode    = Phake::mock(FeatureNode::class);
        $fakeScenario       = Phake::mock(ScenarioNode::class);
        $fakeResult         = Phake::mock(TestResult::class);
        $fakeTeardown       = Phake::mock(Teardown::class);
        $fakeScenarioTested = Phake::mock(ScenarioTested::class);

        Phake::when($fakeResult)->getResultCode()->thenReturn(TestResult::PASSED);
        Phake::when($fakeScenario)->getTitle()->thenReturn("fake title");
        Phake::when($fakeScenario)->getKeyword()->thenReturn("FAKE");
        Phake::when($fakeScenario)->getText()->thenReturn("fake text");
        Phake::when($fakeScenario)->getTags()->thenReturn("fakeTag");
        Phake::when($fakeScenarioTested)->getScenario()->thenReturn($fakeScenario);

        $fakeAfterScenario = new AfterScenarioTested($fakeEnvironment, $fakeFeatureNode, $fakeScenario, $fakeResult, $fakeTeardown);

        $this->triggerUnitEvent(ScenarioTested::BEFORE, $fakeScenarioTested);
        $this->triggerUnitEvent(ScenarioTested::AFTER, $fakeAfterScenario);

        $expectedReport = new BddReportBuilder();
        $expectedReport->startScenario('fake title', 'fakeTag');
        $expectedReport->finishScenario(TestResult::PASSED);

        $this->assertEquals($expectedReport->build()->toArray(), $this->captureReportBuild());
    }

    public function testAfterFeature_BddFrameworkFinishedFeature_ShouldReturnParentNode()
    {
        $fakeFeature       = Phake::mock(FeatureNode::class);
        $fakeFeatureTested = Phake::mock(FeatureTested::class);

        Phake::when($fakeFeature)->getTitle()->thenReturn("fake title");
        Phake::when($fakeFeature)->getDescription()->thenReturn("fake description");
        Phake::when($fakeFeature)->getTags()->thenReturn("fakeTag");
        Phake::when($fakeFeatureTested)->getFeature()->thenReturn($fakeFeature);

        $this->triggerUnitEvent(FeatureTested::BEFORE, $fakeFeatureTested);
        $this->triggerUnitEvent(FeatureTested::AFTER, $fakeFeatureTested);

        $expectedReport = new BddReportBuilder();
        $expectedReport->startFeature("fake title", "fake description", "fakeTag");
        $expectedReport->finishFeature();

        $this->assertEquals($expectedReport->build()->toArray(), $this->captureReportBuild());
    }

    public function testFinishSuite_BddFrameworkFinishedSuite_ShouldReturnNodeWithStatistics()
    {
        // TODO this should not be here
        if (!defined('ATHENA_TESTS_TYPE')) {
            define('ATHENA_TESTS_TYPE', 'unit');
            define('ATHENA_TESTS_DIRECTORY', getcwd());
        }

        $fakeSuite       = Phake::mock(Suite::class);
        $fakeSuiteTested = Phake::mock(SuiteTested::class);

        Phake::when($fakeSuite)->getName()->thenReturn("fake test suite.");
        Phake::when($fakeSuite)->getSetting('paths')->thenReturn([]);
        Phake::when($fakeSuiteTested)->getSuite()->thenReturn($fakeSuite);

        $this->triggerUnitEvent(SuiteTested::BEFORE, $fakeSuiteTested);
        $this->triggerUnitEvent(SuiteTested::AFTER, $fakeSuiteTested);

        $expectedReport = new BddReportBuilder();
        $expectedReport->startSuite("fake test suite.", getcwd());
        $expectedReport->finishSuite(static::EXECUTION_TIME);

        $this->assertEquals($expectedReport->build()->toArray(), $this->captureReportBuild());
    }
}
