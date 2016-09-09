<?php

namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use GuzzleHttp\Message\Response as GuzzleResponse;
use PHPUnit_Framework_MockObject_MockObject;

class StatusCodeAssertionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $responseHolder;

    public function setUp()
    {
        $this->responseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
    }

    public function testMatchingStatusCode()
    {
        $this->responseHolder->method('getStatusCode')->willReturn(200);

        $this->assertTrue((new StatusCodeAssertion(200))->assert($this->responseHolder));
    }

    public function testNotMatchingStatusCode()
    {
        $this->responseHolder->method('getStatusCode')->willReturn(404);

        $this->setExpectedException(UnexpectedValueException::class);
        (new StatusCodeAssertion(200))->assert($this->responseHolder);
    }
}
