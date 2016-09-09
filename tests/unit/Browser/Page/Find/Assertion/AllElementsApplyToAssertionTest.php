<?php
namespace Athena\Tests\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Assertion\AllElementsApplyToAssertion;
use Athena\Exception\NotAllElementsApplyToCriteriaException;
use Athena\Exception\NotAnArrayException;
use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsApplyToAssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDecorate_AllElementsMeetTheCriteria_ShouldReturnTrue()
    {
        $criteria = function (RemoteWebElement $element) {
            return $element->getAttribute('age') === 20;
        };

        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('age')->thenReturn(20);
            \Phake::when($elem2)->getAttribute('age')->thenReturn(20);
            \Phake::when($elem3)->getAttribute('age')->thenReturn(20);

            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsApplyToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     *
     * @expectedException \Athena\Exception\NotAllElementsApplyToCriteriaException
     */
    public function testDecorate_NotAllElementsMeetTheCriteria_ShouldThrowNotAllElementsApplyToCriteriaException()
    {
        $criteria = function (RemoteWebElement $element) {
            return $element->getAttribute('age') === 20;
        };

        $targetClosure = function () {
            $elem1 = \Phake::mock(RemoteWebElement::class);
            $elem2 = \Phake::mock(RemoteWebElement::class);
            $elem3 = \Phake::mock(RemoteWebElement::class);
            \Phake::when($elem1)->getAttribute('age')->thenReturn(20);
            \Phake::when($elem2)->getAttribute('age')->thenReturn(30);
            \Phake::when($elem3)->getAttribute('age')->thenReturn(20);

            return [$elem1, $elem2, $elem3];
        };

        $assertion = new AllElementsApplyToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     *
     * @expectedException \Athena\Exception\EmptyResultException
     */
    public function testDecorate_ClosureReturnsEmptyArray_ShouldThrowEmptyResultException()
    {
        $criteria = function (RemoteWebElement $element) {
            return $element->getAttribute('age') === 20;
        };

        $targetClosure = function () {
            return [];
        };

        $assertion = new AllElementsApplyToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }

    /**
     * @test
     *
     * @expectedException \Athena\Exception\NotAnArrayException
     */
    public function testDecorate_ClosureDoesNotReturnAnArray_ShouldThrowNotAnArrayException()
    {
        $criteria = function ($element) {
            return $element->getAttribute('age') === 20;
        };

        $targetClosure = function () {
            return new \stdClass();
        };

        $assertion = new AllElementsApplyToAssertion($criteria);
        $this->assertTrue($assertion->decorate($targetClosure, null));
    }
}
