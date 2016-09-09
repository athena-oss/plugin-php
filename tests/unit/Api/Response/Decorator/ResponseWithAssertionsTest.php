<?php

namespace Athena\Tests\Api\Response\Decorator;

use Athena\Api\Response\Decorator\ResponseWithAssertions;
use Athena\Api\Response\Response;
use PHPUnit_Framework_MockObject_MockObject;

class ResponseWithAssertionsTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $response;
    /** @var  ResponseWithAssertions */
    private $responseWithAssertions;

    public function setUp()
    {
        $this->response = $this->getMockWithoutInvokingTheOriginalConstructor(Response::class);
        $this->responseWithAssertions = new ResponseWithAssertions($this->response);
    }

    public function testStatusCodeIs()
    {
        $this->responseWithAssertions->statusCodeIs(200);
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testResponseIsJson()
    {
        $this->responseWithAssertions->responseIsJson();
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testHasHeader()
    {
        $this->responseWithAssertions->hasHeader('xpto');
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testHeaderValueIsEqual()
    {
        $this->responseWithAssertions->headerValueIsEqual('header name', 'some value');
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testJsonHasPath()
    {
        $this->responseWithAssertions->jsonHasPath('$.some.path');
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testJsonPathValueIsEqual()
    {
        $this->responseWithAssertions->jsonPathValueIsEqual('$.some.*', 'my value');
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testJsonStructureIs()
    {
        $this->responseWithAssertions->jsonStructureIs([]);
        $this->assertAttributeCount(1, 'responseAssertions', $this->responseWithAssertions);
    }

    public function testRetrieveWithoutValidations()
    {
        $this->response->method('retrieve')->willReturn(1332);
        $this->response->method('getResponseHolder')->willReturnSelf();

        $this->assertEquals(1332, $this->responseWithAssertions->retrieve());
    }
}
