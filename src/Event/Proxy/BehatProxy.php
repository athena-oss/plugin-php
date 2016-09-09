<?php
namespace Athena\Event\Proxy;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Proxy events from Behat to Athena.
 *
 * @package Athena\Event\Proxy
 */
class BehatProxy implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    /**
     * BehatProxy constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::BEFORE       => ['beforeSuite', -50],
            FeatureTested::BEFORE     => ['beforeFeature', -50],
            ScenarioTested::BEFORE    => ['beforeScenario', -50],
            OutlineTested::BEFORE     => ['beforeOutline', -50],
            ExampleTested::BEFORE     => ['beforeExample', -50],
            StepTested::AFTER         => ['afterStep', -50],
            ScenarioTested::AFTER     => ['afterScenario', -50],
            FeatureTested::AFTER      => ['afterFeature', -50],
            SuiteTested::AFTER        => ['afterSuite', -50],
            ExampleTested::AFTER      => ['afterScenario', -50]
        ];
    }

    /**
     * @param \Behat\Testwork\EventDispatcher\Event\SuiteTested $event
     */
    public function beforeSuite(SuiteTested $event)
    {
        $this->eventDispatcher->dispatch(SuiteTested::BEFORE, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\FeatureTested $event
     */
    public function beforeFeature(FeatureTested $event)
    {
        $this->eventDispatcher->dispatch(FeatureTested::BEFORE, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event)
    {
        $this->eventDispatcher->dispatch(ScenarioTested::BEFORE, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\OutlineTested $event
     */
    public function beforeOutline(OutlineTested $event)
    {
        $this->eventDispatcher->dispatch(OutlineTested::BEFORE, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeExample(ScenarioTested $event)
    {
        $this->eventDispatcher->dispatch(ExampleTested::BEFORE, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\StepTested $event
     */
    public function afterStep(StepTested $event)
    {
        $this->eventDispatcher->dispatch(StepTested::AFTER, $event);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function afterScenario(ScenarioTested $event)
    {
        $this->eventDispatcher->dispatch(ScenarioTested::AFTER, $event);
    }

    /**
     * @param mixed $event
     */
    public function afterFeature($event)
    {
        $this->eventDispatcher->dispatch(FeatureTested::AFTER, $event);
    }

    /**
     * @param \Behat\Testwork\EventDispatcher\Event\SuiteTested $event
     */
    public function afterSuite(SuiteTested $event)
    {
        $this->eventDispatcher->dispatch(SuiteTested::AFTER, $event);
    }
}

