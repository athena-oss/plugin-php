<?php

namespace Athena\Tests\Api\Response;

use Athena\Api\Response\ResponseFormatter;
use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;

class ResponseFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ResponseFormatter */
    private $responseWithInvalidJson;
    /** @var  ResponseFormatter */
    private $responseWithValidJson;
    /** @var  ResponseHolder */
    private $validJsonResponseHolder;
    /** @var  ResponseHolder */
    private $invalidJsonResponseHolder;

    public function setUp()
    {
        $this->invalidJsonResponseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
        $this->invalidJsonResponseHolder->method('getBody')->willReturn('} {{{');

        $this->responseWithInvalidJson = new ResponseFormatter($this->invalidJsonResponseHolder);

        $this->validJsonResponseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
        $this->validJsonResponseHolder->method('getBody')->willReturn('{"key": "value"}');

        $this->responseWithValidJson = new ResponseFormatter($this->validJsonResponseHolder);
    }

    public function testFromJsonWithInvalidJsonBody()
    {
        $this->setExpectedException(UnexpectedValueException::class);
        $this->responseWithInvalidJson->fromJson();
    }

    public function testFromJsonWithValidJsonBodyAndExpectAssociativeArray()
    {
        $decodedJson = $this->responseWithValidJson->fromJson();

        $this->assertEquals(['key' => 'value'], $decodedJson);
    }

    public function testFromJsonWithValidJsonBodyAndExpectObject()
    {
        $decodedJson = $this->responseWithValidJson->fromJson(false);

        $expectedObj = new \StdClass;
        $expectedObj->key = 'value';

        $this->assertEquals($expectedObj, $decodedJson);
    }

    public function testFromString()
    {
        $validJson = $this->responseWithValidJson->fromString();
        $invalidJson = $this->responseWithInvalidJson->fromString();

        $this->assertEquals('{"key": "value"}', $validJson);
        $this->assertEquals('} {{{', $invalidJson);
    }

    public function testGetResponse()
    {
        $this->assertSame($this->validJsonResponseHolder, $this->responseWithValidJson->getResponse());
        $this->assertSame($this->invalidJsonResponseHolder, $this->responseWithInvalidJson->getResponse());
    }

    public function testToString()
    {
        $this->assertEquals('{"key": "value"}', (string) $this->responseWithValidJson);
        $this->assertEquals('} {{{', (string) $this->responseWithInvalidJson);
    }
}
