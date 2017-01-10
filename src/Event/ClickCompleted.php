<?php
namespace Athena\Event;

use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use Symfony\Component\EventDispatcher\Event;

class ClickCompleted extends Event
{
    const AFTER = 'browser.click.after';

    /**
     * @var \OLX\FluentWebDriverClient\Browser\BrowserInterface
     */
    private $browser;

    /**
     * ClickCompleted constructor.
     *
     * @param \OLX\FluentWebDriverClient\Browser\BrowserInterface $browser
     */
    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}

