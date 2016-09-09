<?php
namespace Athena\Event;

use Athena\Browser\BrowserInterface;
use Symfony\Component\EventDispatcher\Event;

class ClickCompleted extends Event
{
    const AFTER = 'browser.click.after';

    /**
     * @var \Athena\Browser\BrowserInterface
     */
    private $browser;

    /**
     * ClickCompleted constructor.
     *
     * @param \Athena\Browser\BrowserInterface $browser
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

