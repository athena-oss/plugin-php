<?php
namespace Athena\Api;

use Athena\Event\Adapter\GuzzleReportMiddleware;
use Athena\Event\HttpTransactionCompleted;
use Athena\Translator\UrlTranslator;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use OLX\FluentHttpClient\HttpClient;
use OLX\FluentHttpClient\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @return HttpClientInterface
     */
    public function build()
    {

        $config = [];
        $config['exceptions'] = $this->httpExceptions;
        $config['verify'] = $this->SSLVerification;

        if (!empty($this->proxy)) {
            $config['proxy'] = sprintf('%s:%d', $this->proxy['url'], $this->proxy['internalPort']);
        }

        if ($this->withEventDispatcher) {
            $handler = HandlerStack::create();
            $handler->push(GuzzleReportMiddleware::eventCapture($this->eventDispatcher));

            $config['handler'] = $handler;
        }

        // http client
        $guzzleClient = new Client($config);

        // url translator
        $baseUrlId = UrlTranslator::BASE_URL_IDENTIFIER;
        $baseUrl = array_key_exists($baseUrlId, $this->urls) ? $this->urls[$baseUrlId] : null;
        $urlTranslator = new UrlTranslator($this->urls, $baseUrl);

        return new ApiDecorator(new HttpClient($guzzleClient), $urlTranslator);
    }
}
