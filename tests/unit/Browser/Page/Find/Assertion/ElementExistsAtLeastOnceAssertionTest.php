<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\ElementExistsAtLeastOnceAssertion;
use Facebook\WebDriver\Remote\RemoteWebElement;

class ElementExistsAtLeastOnceAssertionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testDecorate_ElementCountIsNotZero_ShouldReturnTrue()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('age')->thenReturn(20);
            \Phake::when($elem2)->getAttribute('age')->thenReturn(20);
            \Phake::when($elem3)->getAttribute('age')->thenReturn(20);

            return [$elem1, $elem2, $elem3];
        };
        
        $assertion = new ElementExistsAtLeastOnceAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\NoSuchElementException
     */
    public function testDecorate_ElementCountIsZero_ShouldThrowNoSuchElementException()
    {
        $targetClosure = function () {
            return [];
        };

        $assertion = new ElementExistsAtLeastOnceAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
