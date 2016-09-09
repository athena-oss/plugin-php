<?php
namespace Athena\Tests\Browser\Page\Find\Wait;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\Wait\WaitUntilClickable;
use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\WebDriverWait;
use Phake;

class WaitUntilClickableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_NoExceptionIsThrownByWait_ShouldReturnTrue()
    {
        $targetClosure = function () {
            return [1, 2, 3];
        };

        $fakeBrowser    = Phake::mock(BrowserInterface::class);
        $fakeDriverWait = Phake::mock(WebDriverWait::class);
        $fakeWaitUntil  = Phake::partialMock(WaitUntilClickable::class, $fakeBrowser, 10);

        Phake::when($fakeWaitUntil)->validate(Phake::anyParameters())->thenReturn(true);
        Phake::when($fakeDriverWait)->until(Phake::anyParameters())->thenReturn(true);
        Phake::when($fakeBrowser)->wait(Phake::anyParameters())->thenReturn($fakeDriverWait);

        $this->assertTrue($fakeWaitUntil->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\CriteriaNotMetException
     */
    public function testDecorate_ExceptionIsThrownByWait_ShouldThrowCriteriaNotMetException()
    {
        $targetClosure = function () {
            return [];
        };

        $fakeBrowser    = Phake::mock(BrowserInterface::class);
        $fakeDriverWait = Phake::mock(WebDriverWait::class);

        Phake::when($fakeDriverWait)->until(Phake::anyParameters())->thenThrow(new Exception('Something thown by wait'));
        Phake::when($fakeBrowser)->wait(Phake::anyParameters())->thenReturn($fakeDriverWait);

        $wait = new WaitUntilClickable($fakeBrowser, 10);
        $this->assertTrue($wait->decorate($targetClosure, null));
    }
}
