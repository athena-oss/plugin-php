<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 28/01/16
 * Time: 16:33
 */

namespace Athena\Tests\Browser\Page\Element\Find;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Element\Find\ElementFind;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Phake;
use Phake_IMock;

class ElementFindTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Phake_IMock $fakeRemoteWebElement
     *
     * @return \Athena\Browser\Page\Element\Find\ElementFind
     */
    public function makeElementFind(Phake_IMock $fakeRemoteWebElement)
    {
        $fakeFindBy  = Phake::mock(WebDriverBy::class);
        $fakeBrowser = Phake::mock(BrowserInterface::class);

        Phake::when($fakeRemoteWebElement)->getTagName()->thenReturn("select");
        Phake::when($fakeBrowser)->findElement($fakeFindBy)->thenReturn($fakeRemoteWebElement);

        return new ElementFind($fakeFindBy, $fakeBrowser);
    }

    public function testAsDropdown_ElementIsFound_ShouldReturnWebDriverSelectInstance()
    {
        $fakeRemoteWebElement = Phake::mock(RemoteWebElement::class);

        $elementFind = $this->makeElementFind($fakeRemoteWebElement);

        $this->assertInstanceOf(WebDriverSelect::class, $elementFind->asDropDown());
    }

    public function testAsHtmlElement_ElementIsFound_ShouldReturnFoundRemoteWebElementInstance()
    {
        $fakeRemoteWebElement = Phake::mock(RemoteWebElement::class);

        $elementFind = $this->makeElementFind($fakeRemoteWebElement);

        $this->assertSame($fakeRemoteWebElement, $elementFind->asHtmlElement());
    }

    public function testGetWebDriver_WebDriverInjected_ShouldReturnWebDriverInjectedInstance()
    {
        $fakeDriverBy = Phake::mock(WebDriverBy::class);
        $fakeBrowser  = Phake::mock(BrowserInterface::class);

        $elementFind = new ElementFind($fakeDriverBy, $fakeBrowser);

        $this->assertSame($fakeBrowser, $elementFind->getBrowser());
    }

    public function testGetSearchCriteria_WebDriverByInjected_ShouldReturnWebDriverByInjectedInstance()
    {
        $fakeDriverBy = Phake::mock(WebDriverBy::class);
        $fakeBrowser  = Phake::mock(BrowserInterface::class);

        $elementFind = new ElementFind($fakeDriverBy, $fakeBrowser);

        $this->assertSame($fakeDriverBy, $elementFind->getSearchCriteria());
    }
}