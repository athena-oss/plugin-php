<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\AtLeastOneElementAppliesToAssertion;
use Facebook\WebDriver\Remote\RemoteWebElement;

class AtLeastOneElementAppliesToAssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_OneOfElementsAppliesToCriteria_ShouldReturnTrue()
    {
        $criteria = function (RemoteWebElement $element) {
            return $element->isDisplayed() === true;
        };

        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(false);
            \Phake::when($elem2)->isDisplayed()->thenReturn(true);
            \Phake::when($elem3)->isDisplayed()->thenReturn(false);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AtLeastOneElementAppliesToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\NoElementAppliesToCriteriaException
     */
    public function testDecorate_NotAllElementsAreVisible_ShouldThrowNotAllElementsApplyToCriteriaException()
    {
        $criteria = function (RemoteWebElement $element) {
            return $element->isDisplayed() === true;
        };

        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->isDisplayed()->thenReturn(false);
            \Phake::when($elem2)->isDisplayed()->thenReturn(false);
            \Phake::when($elem3)->isDisplayed()->thenReturn(false);
            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AtLeastOneElementAppliesToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
