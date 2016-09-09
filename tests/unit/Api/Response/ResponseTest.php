<?php

namespace Athena\Tests\Api\Response;

use Athena\Api\Response\Decorator\ResponseWithAssertions;
use Athena\Api\Response\Response;
use Athena\Api\Response\ResponseFormatter;
use Athena\Holder\ClosureHolder;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClosureHolder */
    private $closureHolder;
    /** @var Response */
    private $response;

    public function setUp()
    {
        $response = $this->getMockWithoutInvokingTheOriginalConstructor(\GuzzleHttp\Message\Response::class);
        $this->closureHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ClosureHolder::class);
        $this->closureHolder->method('get')->willReturn($response);
        $this->response = new Response($this->closureHolder);
    }

    public function testAssertThat()
    {
        $requestWithAssertions = $this->response->assertThat();

        $this->assertInstanceOf(ResponseWithAssertions::class, $requestWithAssertions);
    }

    public function testRetrieve()
    {
        $responseFormatter = $this->response->retrieve();

        $this->assertInstanceOf(ResponseFormatter::class, $responseFormatter);
    }

    public function testGetClosureHolder()
    {
        $closureHolder = $this->response->getClosureHolder();

        $this->assertSame($this->closureHolder, $closureHolder);
    }
}
