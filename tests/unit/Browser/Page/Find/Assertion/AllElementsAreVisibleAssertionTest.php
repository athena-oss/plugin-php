<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\AllElementsAreVisibleAssertion;
use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsAreVisibleAssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_AllElementsAreVisible_ShouldReturnTrue()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(true);
            \Phake::when($elem2)->isDisplayed()->thenReturn(true);
            \Phake::when($elem3)->isDisplayed()->thenReturn(true);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsAreVisibleAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\NotAllElementsApplyToCriteriaException
     */
    public function testDecorate_NotAllElementsAreVisible_ShouldThrowNotAllElementsApplyToCriteriaException()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(true);
            \Phake::when($elem2)->isDisplayed()->thenReturn(true);
            \Phake::when($elem3)->isDisplayed()->thenReturn(false);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsAreVisibleAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
