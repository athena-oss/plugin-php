<?php
namespace Athena\Browser\Page\Find;

use Athena\Browser\BrowserInterface;
use Facebook\WebDriver\Remote\RemoteWebElement;

interface PageFinderInterface
{
    /**
     * @param $name
     * @return RemoteWebElement
     */
    public function elementWithName($name);

    /**
     * @param $name
     * @return RemoteWebElement[]
     */
    public function elementsWithName($name);

    /**
     * @param $id
     * @return RemoteWebElement
     */
    public function elementWithId($id);

    /**
     * @param $id
     * @return RemoteWebElement[]
     */
    public function elementsWithId($id);

    /**
     * @param $css
     * @return RemoteWebElement
     */
    public function elementWithCss($css);

    /**
     * @param $css
     * @return RemoteWebElement[]
     */
    public function elementsWithCss($css);

    /**
     * @param $xpath
     * @return RemoteWebElement
     */
    public function elementWithXpath($xpath);


    /**
     * @param $xpath
     * @return RemoteWebElement[]
     */
    public function elementsWithXpath($xpath);

    /**
     * @return BrowserInterface
     */
    public function getBrowser();
}

