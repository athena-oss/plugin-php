<?php
namespace Athena\Event\Subscriber;

use Athena\Athena;
use Athena\Event\HttpTransactionCompleted;
use Athena\Event\Proxy\BehatProxy;
use Athena\Logger\Builder\BddReportBuilder;
use Athena\Logger\ImageRepository;
use Athena\Logger\Interpreter\InterpreterInterface;
use Athena\Logger\Timer\TimerInterface;
use Athena\Logger\TrafficLoggerInterface;
use Athena\Stream\OutputStreamInterface;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Behat\Testwork\Tester\Result\TestResult;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BddSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Athena\Logger\Interpreter\InterpreterInterface
     */
    private $interpreter;

    /**
     * @var BddReportBuilder
     */
    private $report;

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var \Athena\Stream\OutputStreamInterface
     */
    private $outputStream;

    /**
     * @var TrafficLoggerInterface
     */
    private $trafficLogger;

    /**
     * @var \Athena\Logger\Timer\TimerInterface
     */
    private $timer;

    /**
     * @var string
     */
    private $currentOutlineTitle;

    /**
     * @var HttpTransactionCompleted[]
     */
    private $afterHttpTransactionEvents = [];

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $events = BehatProxy::getSubscribedEvents();
        $events[HttpTransactionCompleted::AFTER] = 'afterHttpTransaction';

        return $events;
    }

    /**
     * BddSubscriber constructor.
     *
     * @param \Athena\Logger\Interpreter\InterpreterInterface $interpreter
     * @param \Athena\Stream\OutputStreamInterface            $outputStream
     * @param \Athena\Logger\Timer\TimerInterface             $timer
     */
    public function __construct(
        InterpreterInterface $interpreter,
        OutputStreamInterface $outputStream,
        TimerInterface $timer
    ) {
        $this->interpreter = $interpreter;
        $this->outputStream = $outputStream;
        $this->report = new BddReportBuilder();
        $this->timer = $timer;
    }

    /**
     * @param \Athena\Logger\ImageRepository $imageRepository
     *
     * @return $this
     */
    public function setImageRepository(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
        return $this;
    }

    /**
     * @param \Athena\Logger\TrafficLoggerInterface $trafficLogger
     *
     * @return $this
     */
    public function setTrafficLogger(TrafficLoggerInterface $trafficLogger)
    {
        $this->trafficLogger = $trafficLogger;
        return $this;
    }

    /**
     * @param \Behat\Testwork\EventDispatcher\Event\SuiteTested $event
     */
    public function beforeSuite(SuiteTested $event)
    {
        $this->timer->start('suite');
        $this->report->startSuite($event->getSuite()->getName(), current($event->getSuite()->getSetting('paths')));
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\FeatureTested $event
     */
    public function beforeFeature(FeatureTested $event)
    {
        $this->report->startFeature(
            $event->getFeature()->getTitle(),
            $event->getFeature()->getDescription(),
            $event->getFeature()->getTags()
        );
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event)
    {
        $this->timer->start('scenario');
        $this->report->startScenario($event->getScenario()->getTitle(), $event->getScenario()->getTags());

        if ($this->trafficLogger instanceof TrafficLoggerInterface) {
            $this->trafficLogger->start();
        }
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\OutlineTested $event
     */
    public function beforeOutline(OutlineTested $event)
    {
        $this->currentOutlineTitle = $event->getOutline()->getTitle();
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeExample(ScenarioTested $event)
    {
        $this->timer->start('scenario');

        $this->report->startScenario($this->currentOutlineTitle . ' Line #' . $event->getScenario()->getLine(), $event->getScenario()->getTags());

        if ($this->trafficLogger instanceof TrafficLoggerInterface) {
            $this->trafficLogger->start();
        }
    }

    /**
     * @param mixed $event
     */
    public function afterStep(AfterStepTested $event)
    {
        $exceptionType       = null;
        $exceptionMessage    = null;
        $exceptionTrace      = null;
        $resultCode          = $event->getTestResult()->getResultCode();
        $stepText            = sprintf("%s %s", $event->getStep()->getKeyword(), $event->getStep()->getText());
        $valuesTables        = [];

        // not the most pretty implementation
        if ($event->getStep()->hasArguments()) {
            foreach ($event->getStep()->getArguments() as $argument) {
                if ($argument instanceof TableNode) {
                    $valuesTables[] = $argument->getTable();
                }
            }
        }

        if ($resultCode == TestResult::FAILED) {
            /* @var $exception \Exception */
            $exception = $event->getTestResult()->getException();

            $exceptionMessage = $exception->getMessage();
            $exceptionType    = get_class($exception);

            if (method_exists($exception, 'getTraceAsString')) {
                $exceptionTrace = $exception->getTraceAsString();
            }
        }

        $imageFileName = $this->takeScreenshot();
        $httpTransactions = $this->getHttpTransactionEvents();

        $this->report->finishStep($stepText, $valuesTables, $resultCode, $imageFileName, $exceptionMessage, $exceptionTrace, $exceptionType, $httpTransactions);
    }

    /**
     * @param $event
     */
    public function afterScenario($event)
    {
        $trafficLoggerFile = null;
        if ($this->trafficLogger instanceof TrafficLoggerInterface ) {
            $trafficLoggerFile = $this->trafficLogger->write();
        }

        $totalExecTime = $this->timer->stop('scenario');

        $this->report->finishScenario($event->getTestResult()->getResultCode(), $trafficLoggerFile, $totalExecTime);
    }

    /**
     * @param \Behat\Behat\EventDispatcher\Event\FeatureTested $event
     */
    public function afterFeature(FeatureTested $event)
    {
        $this->report->finishFeature();
    }

    /**
     * @param \Behat\Testwork\EventDispatcher\Event\SuiteTested $event
     */
    public function afterSuite(SuiteTested $event)
    {
        $totalExecTime = $this->timer->stop('suite');

        $this->report->finishSuite($totalExecTime);
    }

    /**
     * @param \Athena\Event\HttpTransactionCompleted $event
     */
    public function afterHttpTransaction(HttpTransactionCompleted $event)
    {
        $this->afterHttpTransactionEvents[] = $event;
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        $contents = $this->interpreter->interpret($this->report->build()->toArray());

        $this->outputStream->write($contents);
    }

    /**
     * @return \OLX\FluentWebDriverClient\Browser\BrowserInterface
     */
    public function getBrowser()
    {
        return Athena::browser();
    }

    /**
     * @return string
     */
    private function takeScreenshot()
    {
        if (!($this->imageRepository instanceof ImageRepository)) {
            return null;
        }

        $browser = $this->getBrowser();

        return $this->imageRepository->write($browser->takeScreenshot());
    }

    private function getHttpTransactionEvents()
    {
        $httpTransactions = [];
        if (!empty($this->afterHttpTransactionEvents)) {
            do {
                $httpRequestEvent = array_shift($this->afterHttpTransactionEvents);

                $transaction['request_method'] = $httpRequestEvent->getRequestMethod();
                $transaction['request_url'] = $httpRequestEvent->getRequestUrl();
                $transaction['request'] = utf8_encode((string) $httpRequestEvent->getRequest());
                $transaction['response'] = utf8_encode((string) $httpRequestEvent->getResponse());

                $httpTransactions[] = $transaction;
            } while (!empty($this->afterHttpTransactionEvents));
        }

        return $httpTransactions;
    }
}

