<?php
namespace Athena\Browser\Page\Element\Find;

interface ElementFinderInterface
{
    /**
     * @return \Facebook\WebDriver\WebDriverSelect
     */
    public function asDropDown();

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebElement
     */
    public function asHtmlElement();

    /**
     * @internal
     * @return \Athena\Browser\BrowserInterface
     */
    public function getBrowser();

    /**
     * @internal
     * @return \Facebook\WebDriver\WebDriverBy
     */
    public function getSearchCriteria();
}

