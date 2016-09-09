<?php
namespace Athena\Api;

use Athena\Api\Request\FluentRequest;
use Athena\Api\Request\FluentRequestInterface;
use Athena\Translator\UrlTranslator;
use GuzzleHttp\Client;

class ApiClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var UrlTranslator
     */
    private $urlTranslator;

    /**
     * Client constructor.
     * @param Client $httpClient
     * @param UrlTranslator $urlTranslator
     */
    public function __construct(Client $httpClient, UrlTranslator $urlTranslator)
    {
        $this->httpClient    = $httpClient;
        $this->urlTranslator = $urlTranslator;
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function get($uri)
    {
        $uri = $this->urlTranslator->get($uri);
        return new FluentRequest($this->httpClient, 'GET', $uri);
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function post($uri)
    {
        $uri = $this->urlTranslator->get($uri);
        return new FluentRequest($this->httpClient, 'POST', $uri);
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function put($uri)
    {
        $uri = $this->urlTranslator->get($uri);
        return new FluentRequest($this->httpClient, 'PUT', $uri);
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function delete($uri)
    {
        $uri = $this->urlTranslator->get($uri);
        return new FluentRequest($this->httpClient, 'DELETE', $uri);
    }
}

