<?php
namespace Athena\Page;

use Athena\Browser\CurrentActiveBrowser;

class BasePage extends AbstractPage
{
    /**
     * AbstractPage constructor.
     *
     * @param string $baseUrl
     * @param bool $resetBrowser
     */
    public function __construct($baseUrl, $resetBrowser = false)
    {
        parent::__construct(new CurrentActiveBrowser($resetBrowser), $baseUrl);
    }
}

