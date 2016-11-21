<?php
namespace Athena\Event\Subscriber;

use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use Athena\Event\ClickCompleted;
use Athena\Event\FindElementCompleted;
use Athena\Event\FindElementsCompleted;
use Athena\Event\NavigateToCompleted;
use Athena\Event\SendKeysCompleted;
use Athena\Logger\ImageRepository;

class BrowserSubscriber extends UnitSubscriber
{
    /**
     * @var \Athena\Logger\ImageRepository
     */
    private $screenshotRepository;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        $events = parent::getSubscribedEvents();

        $events[NavigateToCompleted::AFTER]   = ['afterNavigateTo', -50];
        $events[FindElementCompleted::AFTER]  = ['afterFindElement', -50];
        $events[FindElementsCompleted::AFTER] = ['afterFindElements', -50];
        $events[SendKeysCompleted::AFTER]     = ['afterSendKeys', -50];
        $events[ClickCompleted::AFTER]        = ['afterClick', -50];

        return $events;
    }

    /**
     * @param \Athena\Logger\ImageRepository $screenshotRepository
     *
     * @return $this
     */
    public function setScreenshotRepository(ImageRepository $screenshotRepository)
    {
        $this->screenshotRepository = $screenshotRepository;
        return $this;
    }

    /**
     * @param \Athena\Event\NavigateToCompleted $event
     */
    public function afterNavigateTo(NavigateToCompleted $event)
    {
        $screenshotFileName = $this->takeScreenshot($event->getBrowser());

        $this->report->addStep(
            sprintf("Navigated to '%s'.", $event->getUrl()), $screenshotFileName
        );
    }

    /**
     * @param \Athena\Event\FindElementCompleted $event
     */
    public function afterFindElement(FindElementCompleted $event)
    {
        $selectorMechanism  = $event->getLocator()->getMechanism();
        $selectorValue      = $event->getLocator()->getValue();
        $screenshotFileName = $this->takeScreenshot($event->getBrowser());

        $this->report->addStep(
            sprintf("Found element '%s' by %s.", $selectorValue, $selectorMechanism), $screenshotFileName
        );
    }

    /**
     * @param \Athena\Event\FindElementsCompleted $event
     */
    public function afterFindElements(FindElementsCompleted $event)
    {
        $selectorMechanism  = $event->getLocator()->getMechanism();
        $selectorValue      = $event->getLocator()->getValue();
        $screenshotFileName = $this->takeScreenshot($event->getBrowser());

        $this->report->addStep(
            sprintf("Found multiple elements '%s' by %s.", $selectorValue, $selectorMechanism), $screenshotFileName
        );
    }

    /**
     * @param \Athena\Event\SendKeysCompleted $event
     */
    public function afterSendKeys(SendKeysCompleted $event)
    {
        $screenshotFileName = $this->takeScreenshot($event->getBrowser());

        $this->report->addStep(
            sprintf("Changed element value to '%s'.", $event->getValue()), $screenshotFileName
        );
    }

    /**
     * @param \Athena\Event\ClickCompleted $event
     */
    public function afterClick(ClickCompleted $event)
    {
        $screenshotFileName = $this->takeScreenshot($event->getBrowser());

        $this->report->addStep("Clicked element.", $screenshotFileName);
    }

    /**
     * @param \OLX\FluentWebDriverClient\Browser\BrowserInterface $browser
     *
     * @return string
     */
    private function takeScreenshot(BrowserInterface $browser)
    {
        if (!($this->screenshotRepository instanceof ImageRepository)) {
            return null;
        }

        return $this->screenshotRepository->write($browser->takeScreenshot());
    }
}

