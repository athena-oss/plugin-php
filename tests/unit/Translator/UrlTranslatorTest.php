<?php
namespace Athena\Test;

use Athena\Translator\UrlTranslator;

class UrlTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testGet_UrlIsValidUrl_ShouldReturnGivenUrl()
    {
        $urlTranslator = new UrlTranslator([], null);
        $this->assertEquals('http://google.pt', $urlTranslator->get('http://google.pt'));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\InvalidUrlException
     */
    public function testGet_UrlIsKeyAndNoKeyExistsInContainer_ShouldThrowInvalidUrlException()
    {
        $urlTranslator = new UrlTranslator([], null);
        $urlTranslator->get('home');
    }

    /**
     * @test
     */
    public function testGet_UrlIsKeyAndKeyExistsInContainer_ShouldReturnFullUrl()
    {
        $urlTranslator = new UrlTranslator(['myaccount' => '/account'], 'http://google.pt');
        $this->assertEquals('http://google.pt/account', $urlTranslator->get('myaccount'));
    }

    /**
     * @test
     */
    public function testGet_UrlIsKeyAndKeyExistsInContainer_ShouldReturnFullUrlFromCache()
    {
        $urlTranslator = new UrlTranslator(['myaccount' => '/account'], 'http://google.pt');
        $urlTranslator->get('myaccount');

        $this->assertEquals('http://google.pt/account', $urlTranslator->get('myaccount'));
    }

    /**
     * @test
     */
    public function testGet_UrlIsDash_ShouldReturnBaseUrl()
    {
        $urlTranslator = new UrlTranslator([], 'http://google.pt');
        $this->assertEquals('http://google.pt', $urlTranslator->get('/'));
    }

    /**
     * @test
     */
    public function testGet_UrlStartsWithDashAndIsValid_ShouldReturnFullUrl()
    {
        $urlTranslator = new UrlTranslator(['home' => 'http://google.pt'], 'http://google.pt');
        $this->assertEquals('http://google.pt/account', $urlTranslator->get('/account'));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\InvalidUrlException
     */
    public function testGet_UrlStartsWithDashAndBaseUrlIsNotValid_ShouldThrowInvalidUrlException()
    {
        $urlTranslator = new UrlTranslator([], 'google.pt');
        $urlTranslator->get('/');
    }
}
