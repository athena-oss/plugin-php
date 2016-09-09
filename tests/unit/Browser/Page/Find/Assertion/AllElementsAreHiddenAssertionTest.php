<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\AllElementsAreHiddenAssertion;
use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsAreHiddenAssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_AllElementsAreHidden_ShouldReturnTrue()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(false);
            \Phake::when($elem2)->isDisplayed()->thenReturn(false);
            \Phake::when($elem3)->isDisplayed()->thenReturn(false);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsAreHiddenAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\NotAllElementsApplyToCriteriaException
     */
    public function testDecorate_NotAllElementsAreHidden_ShouldThrowNotAllElementsApplyToCriteriaException()
    {
        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(false);
            \Phake::when($elem2)->isDisplayed()->thenReturn(true);
            \Phake::when($elem3)->isDisplayed()->thenReturn(false);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsAreHiddenAssertion();
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
