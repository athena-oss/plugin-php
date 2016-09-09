<?php
namespace Athena\Browser\Page\Element;

use Athena\Browser\BrowserInterface;
use Facebook\WebDriver\WebDriverBy;

class ElementSelector
{
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $browser;

    /**
     * ElementSelectorFactory constructor.
     *
     * @param \Athena\Browser\BrowserInterface $driver
     */
    public function __construct(BrowserInterface $driver)
    {
        $this->browser = $driver;
    }

    /**
     * @param string $name
     *
     * @return \Athena\Browser\Page\Element\ElementAction
     */
    public function withName($name)
    {
        return new ElementAction(WebDriverBy::name($name), $this->browser);
    }

    /**
     * @param string $elementId
     *
     * @return \Athena\Browser\Page\Element\ElementAction
     */
    public function withId($elementId)
    {
        return new ElementAction(WebDriverBy::id($elementId), $this->browser);
    }

    /**
     * @param string $xPath
     *
     * @return \Athena\Browser\Page\Element\ElementAction
     */
    public function withXpath($xPath)
    {
        return new ElementAction(WebDriverBy::xpath($xPath), $this->browser);
    }

    /**
     * @param string $cssSelector
     *
     * @return \Athena\Browser\Page\Element\ElementAction
     */
    public function withCss($cssSelector)
    {
        return new ElementAction(WebDriverBy::cssSelector($cssSelector), $this->browser);
    }

    /**
     * @param $linkText
     * @return ElementAction
     */
    public function withLinkText($linkText){
        return new ElementAction(WebDriverBy::linkText($linkText),$this->browser);
    }
}

