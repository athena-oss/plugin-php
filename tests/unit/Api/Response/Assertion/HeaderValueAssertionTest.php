<?php

namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use GuzzleHttp\Message\Response as GuzzleResponse;
use PHPUnit_Framework_MockObject_MockObject;

class HeaderValueAssertionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $responseHolder;
    /** @var  HeaderAssertion */
    private $headerAssertion;

    public function setUp()
    {
        $this->responseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
        $this->responseHolder->method('getHeader')->willReturnCallback(function ($headerName) {
            $headers = ['Foo' => 'bar'];

            return $headers[$headerName];
        });

        $this->headerAssertion = new HeaderAssertion('my-expected-header-name');
    }

    public function testAssertMatchingHeaderValue()
    {
        $this->assertTrue((new HeaderValueAssertion('Foo', 'bar'))->assert($this->responseHolder));
    }

    public function testAssertNotMatchingHeaderValue()
    {
        $this->setExpectedException(UnexpectedValueException::class);
        (new HeaderValueAssertion('Foo', 'wrong-value'))->assert($this->responseHolder);
    }
}
