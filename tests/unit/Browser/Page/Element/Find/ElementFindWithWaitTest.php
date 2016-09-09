<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 28/01/16
 * Time: 16:44
 */

namespace Athena\Tests\Browser\Page\Element\Find;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Element\Find\ElementFinderInterface;
use Athena\Browser\Page\Element\Find\ElementFindWithWait;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Phake;

class ElementFindWithWaitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Facebook\WebDriver\WebDriverWait $fakeWebDriverWait
     *
     * @return \Athena\Browser\Page\Element\Find\ElementFindWithWait
     */
    public function makeElementFindWithWait(WebDriverWait $fakeWebDriverWait)
    {
        $fakeDriverBy      = Phake::mock(WebDriverBy::class);
        $fakeBrowser       = Phake::mock(BrowserInterface::class);
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);

        Phake::when($fakeBrowser)->wait(Phake::anyParameters())->thenReturn($fakeWebDriverWait);
        Phake::when($fakeElementFinder)->getSearchCriteria()->thenReturn($fakeDriverBy);
        Phake::when($fakeElementFinder)->getBrowser()->thenReturn($fakeBrowser);

        return new ElementFindWithWait(10, $fakeElementFinder);
    }

    public function testToBePresent_MethodIsCalled_ShouldCallWebDriverWaitUntil()
    {
        $fakeWebDriverWait = Phake::mock(WebDriverWait::class);

        $elementFindWaitInstance = $this->makeElementFindWithWait($fakeWebDriverWait);
        $elementFindWaitInstance->toBePresent();

        Phake::verify($fakeWebDriverWait, Phake::times(1))->until(Phake::anyParameters());
    }

    public function testToBeVisible_MethodIsCalled_ShouldCallWebDriverWaitUntil()
    {
        $fakeWebDriverWait = Phake::mock(WebDriverWait::class);

        $elementFindWaitInstance = $this->makeElementFindWithWait($fakeWebDriverWait);
        $elementFindWaitInstance->toBeVisible();

        Phake::verify($fakeWebDriverWait, Phake::times(1))->until(Phake::anyParameters());
    }

    public function testToBeInvisible_MethodIsCalled_ShouldCallWebDriverWaitUntil()
    {
        $fakeWebDriverWait = Phake::mock(WebDriverWait::class);

        $elementFindWaitInstance = $this->makeElementFindWithWait($fakeWebDriverWait);
        $elementFindWaitInstance->toBeInvisible();

        Phake::verify($fakeWebDriverWait, Phake::times(1))->until(Phake::anyParameters());
    }

    public function testToBeClickable_MethodIsCalled_ShouldCallWebDriverWaitUntil()
    {
        $fakeWebDriverWait = Phake::mock(WebDriverWait::class);

        $elementFindWaitInstance = $this->makeElementFindWithWait($fakeWebDriverWait);
        $elementFindWaitInstance->toBeClickable();

        Phake::verify($fakeWebDriverWait, Phake::times(1))->until(Phake::anyParameters());
    }

    public function testToBeSelectable_MethodIsCalled_ShouldCallWebDriverWaitUntil()
    {
        $fakeWebDriverWait = Phake::mock(WebDriverWait::class);

        $elementFindWaitInstance = $this->makeElementFindWithWait($fakeWebDriverWait);
        $elementFindWaitInstance->toBeSelectable();

        Phake::verify($fakeWebDriverWait, Phake::times(1))->until(Phake::anyParameters());
    }

    public function testThenFind_ElementFinderIsInjected_ShouldReturnElementFinderInjectedInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);

        $elementFindWaitInstance = new ElementFindWithWait(10, $fakeElementFinder);

        $this->assertSame($elementFindWaitInstance->thenFind(), $fakeElementFinder);
    }
}