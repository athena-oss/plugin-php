<?php
namespace Athena\Api;

use Athena\Event\Adapter\GuzzleAdapter;
use Athena\Translator\UrlTranslator;
use GuzzleHttp\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use OLX\FluentHttpClient\HttpClient;

class ApiClientBuilder
{
    /**
     * @var array
     */
    private $urls;

    /**
     * @var array
     */
    private $proxy;

    /**
     * @var boolean
     */
    private $httpExceptions = false;

    /**
     * @var bool
     */
    private $withEventDispatcher;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var bool|string
     */
    private $SSLVerification;

    /**
     * @param array $urls
     * @return $this
     */
    public function withUrls(array $urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * @param array $proxy
     * @return $this
     */
    public function withProxy(array $proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @param boolean $allow
     * @return $this
     */
    public function withHttpExceptions($allow)
    {
        $this->httpExceptions = $allow;
        return $this;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     *
     * @return $this
     */
    public function withEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->withEventDispatcher = true;
        $this->eventDispatcher     = $eventDispatcher;
        return $this;
    }

    /**
     * @param $SSLVerify
     * @return $this
     */
    public function withSetSSLVerification($SSLVerify){
        $this->SSLVerification = $SSLVerify;
        return $this;
    }

    /**
     * @return ApiClient
     */
    public function build()
    {
        // http client
        $guzzleClient = new Client();

        if ($this->withEventDispatcher) {
            $guzzleClient->getEmitter()->attach(new GuzzleAdapter($this->eventDispatcher));
        }

        if (!empty($this->proxy)) {
            $guzzleClient->setDefaultOption('proxy', sprintf('%s:%d', $this->proxy['url'], $this->proxy['internalPort']));
        }

        $guzzleClient->setDefaultOption('exceptions', $this->httpExceptions);
        $guzzleClient->setDefaultOption('verify', $this->SSLVerification);

        // url translator
        $baseUrlId           = UrlTranslator::BASE_URL_IDENTIFIER;
        $baseUrl             = array_key_exists($baseUrlId, $this->urls) ? $this->urls[$baseUrlId] : null;
        $urlTranslator       = new UrlTranslator($this->urls, $baseUrl);

        return new ApiDecorator(new HttpClient($guzzleClient), $urlTranslator);
    }
}
