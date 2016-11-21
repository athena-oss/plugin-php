<?php
namespace Athena\Page;

use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use OLX\FluentWebDriverClient\Browser\Page\Find\Decorator\CachedPageFinderDecorator;
use OLX\FluentWebDriverClient\Browser\Page\Find\PageFinderInterface;
use OLX\FluentWebDriverClient\Browser\Page\PageInterface;

abstract class AbstractPage
{
    /**
     * @var BrowserInterface
     */
    private $browser;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var PageInterface
     */
    private $page;

    /**
     * @var PageFinderInterface
     */
    private $cachedPageFinder;

    /**
     * AbstractPage constructor.
     *
     * @param BrowserInterface $browser
     * @param string           $baseUrl
     */
    public function __construct($browser, $baseUrl)
    {
        $this->browser = $browser;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->browser          = null;
        $this->page             = null;
        $this->cachedPageFinder = null;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrlTranslated()
    {
        return $this->getBrowser()->getUrlTranslator()->get($this->baseUrl);
    }

    /**
     * @param BrowserInterface $browser
     */
    public function setBrowser($browser)
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

    /**
     * @param bool $asNewPage
     *
     * @return $this
     */
    public function open($asNewPage = true)
    {
        if ($asNewPage) {
            $this->page = $this->browser->get($this->baseUrl);
        } else {
            $this->page = $this->browser->getCurrentPage();
        }

        return $this;
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->browser->close();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->browser->getTitle();
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->browser->getPageSource();
    }

    /**
     * Switch to LAST opened window
     */
    protected function openLastWindow()
    {
        $windows = $this->browser->getWindowHandles();
        $this->browser->switchTo()->window(end($windows));
    }

    /**
     * @return \Facebook\WebDriver\WebDriverNavigation
     */
    protected function goBack()
    {
        return $this->browser->navigate()->back();
    }

    /**
     * @return \Facebook\WebDriver\WebDriverNavigation
     */
    protected function goForward()
    {
        return $this->browser->navigate()->forward();
    }

    /**
     * @param $url
     * @return \Facebook\WebDriver\WebDriverNavigation
     */
    protected function goToUrl($url)
    {
        return $this->browser->navigate()->to($url);
    }

    /**
     * @return \Facebook\WebDriver\WebDriverNavigation
     */
    protected function refresh()
    {
        return $this->browser->navigate()->refresh();
    }

    /**
     * @return PageInterface
     */
    protected function page()
    {
        if (is_null($this->page)) {
            $this->open(false);
        }
        return $this->page;
    }

    /**
     * @return PageFinderInterface
     */
    protected function get()
    {
        if (is_null($this->cachedPageFinder)) {
            $this->cachedPageFinder = new CachedPageFinderDecorator($this->page()->find());
        }
        return $this->cachedPageFinder;
    }
}

