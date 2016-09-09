<?php

namespace Athena\Tests\Api;

use Athena\Api\Request\FluentRequest;
use Athena\Exception\UnsupportedOperationException;
use GuzzleHttp\Client;

class FluentRequestTest extends \PHPUnit_Framework_TestCase
{
    private function makeFluentRequestFor($method)
    {
        $httpClient = $this->getMock(Client::class);
        return new FluentRequest($httpClient, $method, 'http://www.olx.com');
    }

    public function testWithBodyGivenMethodIsGET()
    {
        $this->setExpectedException(UnsupportedOperationException::class);

        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withBody('', '');
    }

    public function testWithBodyGivenMethodIsHEAD()
    {
        $this->setExpectedException(UnsupportedOperationException::class);

        $requestObj = $this->makeFluentRequestFor('HEAD');
        $requestObj->withBody('', '');
    }

    public function testWithBodyGivenContentIsArray()
    {
        $requestObj = $this->makeFluentRequestFor('POST');
        $requestObj->withBody(['my-content', ['xpto' => 'ole']], 'my-content-type');

        $expectedOptions = [
            'body' => '0=my-content&1%5Bxpto%5D=ole',
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithBodyGivenContentIsNotArray()
    {
        $requestObj = $this->makeFluentRequestFor('PUT');
        $requestObj->withBody('my content', 'custom content type');

        $expectedOptions = [
            'body' => 'my content',
            'headers' => ['Content-Type' => 'custom content type']
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithHeader()
    {
        $requestObj = $this->makeFluentRequestFor('POST');
        $requestObj->withHeader('My-Header', 'some random stuff');

        $expectedOptions = [
            'headers' => ['My-Header' => 'some random stuff']
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithParameters()
    {
        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withParameters(['param1'=>'valueeee', 'param2'=>'value']);

        $expectedOptions = [
            'query' => ['param1'=>'valueeee', 'param2'=>'value'],
            'headers' => []
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithBasicAuth()
    {
        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withBasicAuth('myusername', 'somepassword');

        $expectedOptions = [
            'auth' => ['myusername', 'somepassword', 'Basic'],
            'headers' => []
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithDigestAuth()
    {
        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withDigestAuth('myusername', 'somepassword');

        $expectedOptions = [
            'auth' => ['myusername', 'somepassword', 'Digest'],
            'headers' => []
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithUserAgent()
    {
        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withUserAgent('my user agent');

        $expectedOptions = [
            'headers' => ['User-Agent' => 'my user agent']
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithOption()
    {
        $requestObj = $this->makeFluentRequestFor('DELETE');
        $requestObj->withOption('someoption', 'randomvalue');
        $requestObj->withOption('someotheroption', ['1', 2]);

        $expectedOptions = [
            'headers' => [],
            'someoption' => 'randomvalue',
            'someotheroption' => ['1', 2]
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }

    public function testWithProxy()
    {
        $requestObj = $this->makeFluentRequestFor('GET');
        $requestObj->withProxy('myproxyhost', 233302);

        $expectedOptions = [
            'headers' => [],
            'proxy' => 'tcp://myproxyhost:233302',
        ];

        $this->assertAttributeEquals($expectedOptions, 'options', $requestObj);
    }
}
