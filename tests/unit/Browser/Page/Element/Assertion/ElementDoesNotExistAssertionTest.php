<?php

namespace Athena\Tests\Browser\Page\Element\Assertion;

use Athena\Browser\Page\Element\Assertion\ElementDoesNotExistAssertion;
use Athena\Browser\Page\Element\Find\ElementFinderInterface;
use Athena\Exception\ElementNotExpectedException;
use Athena\Exception\NoSuchElementException;
use Athena\Exception\StopChainException;

class ElementDoesNotExistAssertionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElementDoesNotExistAssertion
     */
    private $elementDoesNotExistAssertion;

    public function setUp()
    {
        $elementFinder = $this->getMockWithoutInvokingTheOriginalConstructor(ElementFinderInterface::class);
        $this->elementDoesNotExistAssertion = new ElementDoesNotExistAssertion($elementFinder);
    }

    public function testAssertGivenElementExistsShouldThrowException()
    {
        $this->setExpectedException(ElementNotExpectedException::class);
        $this->elementDoesNotExistAssertion->assert(function(){
            return ['element'];
        });
    }

    public function testAssertGivenElementDoesNotExistShouldReturnElement()
    {
        $this->assertEquals($this->elementDoesNotExistAssertion->assert(function() {
            return [];
        }), []);
    }

    public function testAssertGivenNoSuchElementExceptionShouldThrowStopChainException()
    {
        $this->setExpectedException(StopChainException::class);
        $this->elementDoesNotExistAssertion->assert(function() {
            throw new NoSuchElementException();
        });
    }
}
