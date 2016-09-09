<?php
namespace Athena\Browser\Page\Element\Find;

use Athena\Browser\BrowserInterface;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class ElementFind implements ElementFinderInterface
{
    /**
     * @var \Facebook\WebDriver\WebDriverBy
     */
    private $findBy;
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $browser;

    /**
     * ElementFind constructor.
     *
     * @param \Facebook\WebDriver\WebDriverBy  $findBy
     * @param \Athena\Browser\BrowserInterface $browser
     */
    public function __construct(WebDriverBy $findBy, BrowserInterface $browser)
    {
        $this->findBy = $findBy;
        $this->browser = $browser;
    }

    /**
     * @return \Facebook\WebDriver\WebDriverSelect
     */
    public function asDropDown()
    {
        return new WebDriverSelect($this->findElement());
    }

    /**
     * @return \Facebook\WebDriver\Support\Events\EventFiringWebElement
     */
    public function asHtmlElement()
    {
        return $this->findElement();
    }

    /**
     * @return \Facebook\WebDriver\Support\Events\EventFiringWebElement
     */
    private function findElement()
    {
        return $this->browser->findElement($this->findBy);
    }

    /**
     * @internal
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @internal
     * @return \Facebook\WebDriver\WebDriverBy
     */
    public function getSearchCriteria()
    {
        return $this->findBy;
    }
}

