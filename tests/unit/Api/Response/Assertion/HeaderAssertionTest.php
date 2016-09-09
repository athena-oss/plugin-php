<?php

namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use PHPUnit_Framework_MockObject_MockObject;

class HeaderAssertionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $responseHolder;
    /** @var  HeaderAssertion */
    private $headerAssertion;

    public function setUp()
    {
        $this->responseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
        $this->headerAssertion = new HeaderAssertion('my-expected-header-name');
    }

    public function testAssertWithNonExistingHeader()
    {
        $this->responseHolder->method('getHeader')->willReturn(false);

        $this->setExpectedException(UnexpectedValueException::class);
        $this->headerAssertion->assert($this->responseHolder);
    }

    public function testAssertWithExistingHeader()
    {
        $this->responseHolder->method('getHeader')->willReturn(true);

        $this->assertTrue($this->headerAssertion->assert($this->responseHolder));
    }
}
