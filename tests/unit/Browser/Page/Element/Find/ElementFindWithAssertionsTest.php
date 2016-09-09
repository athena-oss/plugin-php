<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 28/01/16
 * Time: 18:45
 */

namespace Athena\Tests\Browser\Page\Element\Find;

use Athena\Browser\Page\Element\Assertion\ElementDoesNotExistAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsDisplayedAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsEnabledAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsHiddenAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsSelectedAssertion;
use Athena\Browser\Page\Element\Assertion\ElementTextEqualsToAssertion;
use Athena\Browser\Page\Element\Assertion\ElementValueEqualsToAssertion;
use Athena\Browser\Page\Element\Find\ElementFinderInterface;
use Athena\Browser\Page\Element\Find\ElementFindWithAssertions;
use Phake;

class ElementFindWithAssertionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesNotExist_MethodIsCalled_ShouldChangeElementFinderPropertyToElementDoesNotExistAssertionInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementDoesNotExistAssertion($fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->doesNotExist();

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testIsDisplayed_MethodIsCalled_ShouldChangeElementFinderPropertyToElementIsDisplayedAssertionInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementIsDisplayedAssertion($fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->isDisplayed();

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testIsHidden_MethodIsCalled_ShouldChangeElementFinderPropertyToElementIsHiddenAssertionInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementIsHiddenAssertion($fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->isHidden();

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testIsEnabled_MethodIsCalled_ShouldChangeElementFinderPropertyToElementIsEnabledAssertionInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementIsEnabledAssertion($fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->isEnabled();

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testIsSelected_MethodIsCalled_ShouldChangeElementFinderPropertyToElementIsSelectedAssertionInstance()
    {
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementIsSelectedAssertion($fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->isSelected();

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testValueEqualTo_MethodIsCalledWithExpectedValue_ShouldChangeElementFinderPropertyToElementValueEqualsToAssertionInstance()
    {
        $expectedString    = 'my string';
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementValueEqualsToAssertion($expectedString, $fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->valueEqualTo($expectedString);

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }

    public function testTextEqualTo_MethodIsCalledWithExpectedValue_ShouldChangeElementFinderPropertyToElementTextEqualsToAssertionInstance()
    {
        $expectedString    = 'my string';
        $fakeElementFinder = Phake::mock(ElementFinderInterface::class);
        $expectedDecorator = new ElementTextEqualsToAssertion($expectedString, $fakeElementFinder);

        $elementFinder = new ElementFindWithAssertions($fakeElementFinder);
        $elementFinder->textEqualTo($expectedString);

        $this->assertEquals($expectedDecorator, $elementFinder->thenFind());
    }
}
