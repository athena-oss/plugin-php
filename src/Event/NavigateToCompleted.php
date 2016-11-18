<?php
namespace Athena\Event;

use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use Symfony\Component\EventDispatcher\Event;

class NavigateToCompleted extends Event
{
    const AFTER = 'browser.navigate_to.after';

    /**
     * @var \OLX\FluentWebDriverClient\Browser\BrowserInterface
     */
    private $browser;

    /**
     * @var string
     */
    private $url;

    /**
     * NavigateTo constructor.
     *
     * @param string                           $url
     * @param \OLX\FluentWebDriverClient\Browser\BrowserInterface $browser
     */
    public function __construct($url, BrowserInterface $browser)
    {
        $this->browser = $browser;
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}

