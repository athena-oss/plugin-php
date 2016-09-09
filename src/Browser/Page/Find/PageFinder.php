<?php
namespace Athena\Browser\Page\Find;

use Athena\Browser\BrowserInterface;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

class PageFinder implements PageFinderInterface
{
    /**
     * @var BrowserInterface
     */
    private $browser;

    /**
     * PageFinder constructor.
     *
     * @param BrowserInterface $browser
     */
    public function __construct(BrowserInterface $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param $name
     * @return RemoteWebElement
     */
    public function elementWithName($name)
    {
        return $this->browser->findElement(WebDriverBy::name($name));
    }

    /**
     * @param $name
     * @return RemoteWebElement[]
     */
    public function elementsWithName($name)
    {
        return $this->browser->findElements(WebDriverBy::name($name));
    }

    /**
     * @param $id
     * @return RemoteWebElement
     */
    public function elementWithId($id)
    {
        return $this->browser->findElement(WebDriverBy::id($id));
    }

    /**
     * @param $id
     * @return RemoteWebElement[]
     */
    public function elementsWithId($id)
    {
        return $this->browser->findElements(WebDriverBy::id($id));
    }

    /**
     * @param $css
     * @return RemoteWebElement
     */
    public function elementWithCss($css)
    {
        return $this->browser->findElement(WebDriverBy::cssSelector($css));
    }

    /**
     * @param $css
     * @return RemoteWebElement[]
     */
    public function elementsWithCss($css)
    {
        return $this->browser->findElements(WebDriverBy::cssSelector($css));
    }

    /**
     * @param $xpath
     * @return RemoteWebElement
     */
    public function elementWithXpath($xpath)
    {
        return $this->browser->findElement(WebDriverBy::xpath($xpath));
    }

    /**
     * @param $xpath
     * @return RemoteWebElement[]
     */
    public function elementsWithXpath($xpath)
    {
        return $this->browser->findElements(WebDriverBy::xpath($xpath));
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}

