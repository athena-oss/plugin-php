<?php
namespace Athena\Browser\Page\Find\Decorator;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\PageFinderInterface;
use Athena\Browser\Page\Find\Wait\WaitUntilAbsence;
use Athena\Browser\Page\Find\Wait\WaitUntilClickable;
use Athena\Browser\Page\Find\Wait\WaitUntilPresence;
use Athena\Browser\Page\Find\Wait\WaitUntilVisibility;
use Facebook\WebDriver\WebDriverExpectedCondition;

class PageFinderWithWaits extends AbstractPageFinderDecorator
{
    /**
     * @var int
     */
    private $timeOutInSeconds;

    /**
     * PageFinderWithWaits constructor.
     * @param PageFinderInterface $pageFinder
     * @param $timeOutInSeconds
     */
    public function __construct(PageFinderInterface $pageFinder, $timeOutInSeconds)
    {
        parent::__construct($pageFinder);
        $this->timeOutInSeconds = $timeOutInSeconds;
    }

    /**
     * @return $this
     */
    public function untilPresenceOf()
    {
        $this->registerDecorator(new WaitUntilPresence($this->getPageFinder()->getBrowser(), $this->timeOutInSeconds));
        return $this;
    }

    /**
     * @return $this
     */
    public function untilAbsenceOf()
    {
        $this->registerDecorator(new WaitUntilAbsence($this->getPageFinder()->getBrowser(), $this->timeOutInSeconds));
        return $this;
    }

    /**
     * @return $this
     */
    public function untilVisibilityOf()
    {
        $this->registerDecorator(new WaitUntilVisibility($this->getPageFinder()->getBrowser(), $this->timeOutInSeconds));
        return $this;
    }

    /**
     * @return $this
     */
    public function untilClickabilityOf()
    {
        $this->registerDecorator(new WaitUntilClickable($this->getPageFinder()->getBrowser(), $this->timeOutInSeconds));
        return $this;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->getPageFinder()->getBrowser();
    }

    /**
     * @param WebDriverExpectedCondition $expectedCondition
     *
     * @return void
     *
     * @throws \Exception
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function forExpectedCondition(WebDriverExpectedCondition $expectedCondition, $message = "")
    {
        $this->getBrowser()->wait($this->timeOutInSeconds)->until($expectedCondition, $message);
    }
}

