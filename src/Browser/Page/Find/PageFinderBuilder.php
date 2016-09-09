<?php
namespace Athena\Browser\Page\Find;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\Decorator\PageFinderWithAssertions;
use Athena\Browser\Page\Find\Decorator\PageFinderWithWaits;

class PageFinderBuilder
{
    /**
     * @var BrowserInterface
     */
    private $browser;

    /**
     * @var boolean
     */
    private $isWithAssertions;

    /**
     * @var boolean
     */
    private $isWithWaits;

    /**
     * @var int
     */
    private $timeOutInSeconds;

    /**
     * PageFinderBuilder constructor.
     * @param BrowserInterface $remoteWebDriver
     */
    public function __construct(BrowserInterface $remoteWebDriver)
    {
        $this->browser = $remoteWebDriver;
        $this->isWithWaits = false;
        $this->isWithAssertions = false;
    }

    /**
     * @return $this
     */
    public function withAssertions()
    {
        $this->isWithAssertions = true;

        return $this;
    }

    /**
     * @param $timeOutInSeconds
     * @return $this
     */
    public function withWaits($timeOutInSeconds)
    {
        $this->isWithWaits = true;
        $this->timeOutInSeconds = $timeOutInSeconds;

        return $this;
    }

    /**
     * @return PageFinderInterface
     */
    public function build()
    {
        $pageFinder = new PageFinder($this->browser);

        if ($this->isWithAssertions) {
            $pageFinder = new PageFinderWithAssertions($pageFinder);
        }

        if ($this->isWithWaits) {
            $pageFinder = new PageFinderWithWaits($pageFinder, $this->timeOutInSeconds);
        }

        return $pageFinder;
    }
}

