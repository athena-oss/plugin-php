<?php
namespace Athena\Tests\Browser\Page\Find\Wait;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\Wait\WaitUntilAbsence;
use Exception;
use Facebook\WebDriver\WebDriverWait;
use Phake;

class WaitUntilAbsenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_NoExceptionIsThrownByWait_ShouldReturnTrue()
    {
        $targetClosure = function () {
            return [];
        };

        $fakeBrowser    = Phake::mock(BrowserInterface::class);
        $fakeDriverWait = Phake::mock(WebDriverWait::class);

        Phake::when($fakeDriverWait)->until(Phake::anyParameters())->thenReturn(true);
        Phake::when($fakeBrowser)->wait(Phake::anyParameters())->thenReturn($fakeDriverWait);

        $wait = new WaitUntilAbsence($fakeBrowser, 10);
        $this->assertTrue($wait->decorate($targetClosure, null));
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

        $wait = new WaitUntilAbsence($fakeBrowser, 10);
        $this->assertTrue($wait->decorate($targetClosure, null));
    }
}
