<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 26/01/16
 * Time: 16:32
 */

namespace Athena\Tests\Browser\Page\Element;


use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Element\ElementAction;
use Athena\Browser\Page\Element\Find\ElementFind;
use Athena\Browser\Page\Element\Find\ElementFindWithAssertions;
use Athena\Browser\Page\Element\Find\ElementFindWithWait;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Phake;
use PHPUnit_Framework_TestCase;

class ElementActionTest extends PHPUnit_Framework_TestCase
{
    public function testThenFind_FakeDependencies_ShouldReturnElementFindInstance()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);
        $fakeWebDriver   = Phake::mock(BrowserInterface::class);

        $elementAction = new ElementAction($fakeWebDriverBy, $fakeWebDriver);
        $elementFind   = new ElementFind($fakeWebDriverBy, $fakeWebDriver);

        $this->assertEquals($elementFind, $elementAction->thenFind());
    }

    public function testAssertThat_FakeDependencies_ShouldReturnElementFindWithAssertionsInstance()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);
        $fakeWebDriver   = Phake::mock(BrowserInterface::class);

        $elementAction = new ElementAction($fakeWebDriverBy, $fakeWebDriver);
        $elementFindWithAssertions = new ElementFindWithAssertions($elementAction->thenFind());

        $this->assertEquals($elementFindWithAssertions, $elementAction->assertThat());
    }

    public function testWait_FakeDependencies_ShouldReturnElementFindWithAssertionsInstance()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);
        $fakeWebDriver   = Phake::mock(BrowserInterface::class);

        $elementAction = new ElementAction($fakeWebDriverBy, $fakeWebDriver);
        $elementFindWithWait = new ElementFindWithWait(10, $elementAction->thenFind());

        $this->assertEquals($elementFindWithWait, $elementAction->wait(10));
    }
}