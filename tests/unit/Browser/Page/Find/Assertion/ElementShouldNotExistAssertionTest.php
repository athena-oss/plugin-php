<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\ElementShouldNotExistAssertion;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebElement;

class ElementShouldNotExistAssertionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \Athena\Exception\ElementNotExpectedException
     */
    public function testDecorate_ElementCountIsNotZero_ShouldThrowElementNotExpectedException()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('age')->thenReturn(20);

            return [$elem1];
        };
        
        $assertion = new ElementShouldNotExistAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\StopChainException
     */
    public function testDecorate_NoSuchElementExceptionIsThrown_ShouldThrowStopChainException()
    {
        $targetClosure = function () {
            throw new NoSuchElementException('abcd');
        };

        $assertion = new ElementShouldNotExistAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
