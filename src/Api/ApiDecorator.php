<?php
namespace Athena\Api;

use Athena\Translator\UrlTranslator;
use OLX\FluentHttpClient\HttpClientInterface;

class ApiDecorator implements HttpClientInterface
{
    /**
     * @var UrlTranslator
     */
    private $urlTranslator;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * ApiDecorator constructor.
     * @param HttpClientInterface $httpClient
     * @param UrlTranslator $urlTranslator
     */
    public function __construct(HttpClientInterface $httpClient, UrlTranslator $urlTranslator)
    {
        $this->urlTranslator = $urlTranslator;
        $this->httpClient = $httpClient;
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function get($uri)
    {
        $uri = $this->urlTranslator->get($uri);
        return $this->httpClient->get($uri);
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function post($uri)
    {
        return $this->httpClient->post($this->urlTranslator->get($uri));
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function put($uri)
    {
        return $this->httpClient->put($this->urlTranslator->get($uri));
    }

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function delete($uri)
    {
        return $this->httpClient->delete($this->urlTranslator->get($uri));
    }
    
    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function patch($uri)
    {
        return $this->httpClient->patch($this->urlTranslator->get($uri));
    }
    
    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function head($uri)
    {
        return $this->httpClient->head($this->urlTranslator->get($uri));
    }
}
