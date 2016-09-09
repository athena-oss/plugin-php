<?php

namespace Athena\Tests\Event\Proxy;

use Athena\Event\Proxy\BehatProxy;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Phake;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BehatProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Phake_IMock
     */
    private $fakeEventDispatcher;
    /**
     * @var BehatProxy
     */
    private $actualBehatProxy;
    /**
     * @var EventDispatcher
     */
    private $behatEventDispatcher;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->fakeEventDispatcher  = Phake::mock(EventDispatcher::class);
        $this->actualBehatProxy     = new BehatProxy($this->fakeEventDispatcher);

        $this->behatEventDispatcher = new EventDispatcher();
        $this->behatEventDispatcher->addSubscriber($this->actualBehatProxy);
    }

    /**
     * @param string $eventName
     * @param object $event
     */
    private function triggerBehatEvent($eventName, $event)
    {
        $this->behatEventDispatcher->dispatch($eventName, $event);
    }

    /**
     * @param string $eventName
     * @param object $event
     */
    private function assertProxyDispatched($eventName, $event)
    {
        Phake::verify($this->fakeEventDispatcher)->dispatch($eventName, $event);
    }

    public function testBeforeSuite_BehatSuiteStarts_ShouldDispatchSuiteTestedBeforeEvent()
    {
        $fakeSuiteTested = Phake::mock(SuiteTested::class);

        $this->triggerBehatEvent(SuiteTested::BEFORE, $fakeSuiteTested);

        $this->assertProxyDispatched(SuiteTested::BEFORE, $fakeSuiteTested);
    }

    public function testBeforeFeature_BehatFeatureStarts_ShouldDispatchFeatureTestedBeforeEvent()
    {
        $fakeFeatureTested = Phake::mock(FeatureTested::class);

        $this->triggerBehatEvent(FeatureTested::BEFORE, $fakeFeatureTested);

        $this->assertProxyDispatched(FeatureTested::BEFORE, $fakeFeatureTested);
    }

    public function testBeforeScenario_BehatScenarioStarts_ShouldDispatchScenarioTestedBeforeEvent()
    {
        $fakeScenarioTested = Phake::mock(ScenarioTested::class);

        $this->triggerBehatEvent(ScenarioTested::BEFORE, $fakeScenarioTested);

        $this->assertProxyDispatched(ScenarioTested::BEFORE, $fakeScenarioTested);
    }

    public function testAfterStep_BehatFinishedStep_ShouldDispatchStepTestedAfterEvent()
    {
        $fakeStepTested = Phake::mock(StepTested::class);

        $this->triggerBehatEvent(StepTested::AFTER, $fakeStepTested);

        $this->assertProxyDispatched(StepTested::AFTER, $fakeStepTested);
    }

    public function testAfterScenario_BehatFinishedScenario_ShouldDispatchScenarioTestedAfterEvent()
    {
        $fakeScenarioTested = Phake::mock(ScenarioTested::class);

        $this->triggerBehatEvent(ScenarioTested::AFTER, $fakeScenarioTested);

        $this->assertProxyDispatched(ScenarioTested::AFTER, $fakeScenarioTested);
    }

    public function testAfterFeature_BehatFinishedFeature_ShouldDispatchFeatureTestedAfterEvent()
    {
        $fakeFeatureTested = Phake::mock(FeatureTested::class);

        $this->triggerBehatEvent(FeatureTested::AFTER, $fakeFeatureTested);

        $this->assertProxyDispatched(FeatureTested::AFTER, $fakeFeatureTested);
    }

    public function testAfterSuite_BehatFinishedSuite_ShouldDispatchSuiteTestedAfterEvent()
    {
        $fakeSuiteTested = Phake::mock(SuiteTested::class);

        $this->triggerBehatEvent(SuiteTested::AFTER, $fakeSuiteTested);

        $this->assertProxyDispatched(SuiteTested::AFTER, $fakeSuiteTested);
    }
}
