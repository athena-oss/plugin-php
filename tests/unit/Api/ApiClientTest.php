<?php

namespace Athena\Tests\Api;

use Athena\Api\ApiClient;
use Athena\Translator\UrlTranslator;
use GuzzleHttp\Client;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApiClient */
    private $apiClient;

    public function setUp()
    {
        $httpClient = $this->getMock(Client::class);
        $urlTranslator = $this->getMock(UrlTranslator::class, [], [[], '/']);
        $urlTranslator->method('get')->willReturnArgument(0);
        $this->apiClient = new ApiClient($httpClient, $urlTranslator);
    }

    public function testGetMethodIsCalledShouldReturnFluentRequestWithHTTPGetMethod()
    {
        $requestObj = $this->apiClient->get('http://www.olx.com');

        $this->assertAttributeEquals('GET', 'method', $requestObj);
        $this->assertAttributeEquals('http://www.olx.com', 'uri', $requestObj);
    }

    public function testPostMethodIsCalledShouldReturnFluentRequestWithHTTPPostMethod()
    {
        $requestObj = $this->apiClient->post('http://www.olx.com');

        $this->assertAttributeEquals('POST', 'method', $requestObj);
        $this->assertAttributeEquals('http://www.olx.com', 'uri', $requestObj);
    }

    public function testPutMethodIsCalledShouldReturnFluentRequestWithHTTPPutMethod()
    {
        $requestObj = $this->apiClient->put('http://www.olx.com');

        $this->assertAttributeEquals('PUT', 'method', $requestObj);
        $this->assertAttributeEquals('http://www.olx.com', 'uri', $requestObj);
    }

    public function testDeleteMethodIsCalledShouldReturnFluentRequestWithHTTPDeleteMethod()
    {
        $requestObj = $this->apiClient->delete('http://www.olx.com');

        $this->assertAttributeEquals('DELETE', 'method', $requestObj);
        $this->assertAttributeEquals('http://www.olx.com', 'uri', $requestObj);
    }
}
