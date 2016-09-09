<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\ElementExistsAtLeastOnceAssertion;
use Athena\Browser\Page\Find\Assertion\ElementShouldNotExistAssertion;
use Athena\Browser\Page\Find\Assertion\ElementTextEqualsAssertion;
use Athena\Browser\Page\Find\Assertion\ElementValueEqualsAssertion;
use Facebook\WebDriver\Remote\RemoteWebElement;

class ElementValueEqualsAssertionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testDecorate_GetAttributeReturnsSameValueAsExpected_ShouldReturnTrue()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('value')->thenReturn('Ipsos Lorem');
            return $elem1;
        };
        
        $assertion = new ElementValueEqualsAssertion('Ipsos Lorem');
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\UnexpectedValueException
     */
    public function testDecorate_GetAttributetReturnsDifferentValueThanExpected_ShouldThrowUnexpectedValueException()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('value')->thenReturn('Ipsos Lorem');
            return $elem1;
        };

        $assertion = new ElementValueEqualsAssertion('not what i am expecting');
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
