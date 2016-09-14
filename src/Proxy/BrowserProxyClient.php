<?php
namespace Athena\Proxy;

use GuzzleHttp\Client;

class BrowserProxyClient
{
    const ENDPOINT_PROXY     = '/proxy/';
    const ENDPOINT_HOSTS     = '/proxy/%d/hosts';
    const ENDPOINT_TIMEOUT   = '/proxy/%d/timeout';
    const ENDPOINT_HAR       = '/proxy/%d/har';
    const ENDPOINT_PAGEREF   = '/proxy/%d/har/pageRef';
    const ENDPOINT_BLACKLIST = '/proxy/%d/blacklist';
    const ENDPOINT_CACHE     = '/proxy/%d/dns/cache';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $port;

    /**
     * @var int
     */
    private $proxyPort;

    /**
     * @var boolean
     */
    private $hasBeenInited;

    /**
     * @var boolean
     */
    private $isTrustAllServersEnabled;

    /**
     * BrowserProxyClient constructor.
     * @param string $url
     * @param int $port
     */
    public function __construct($url, $port)
    {
        $this->url           = $url;
        $this->port          = $port;
        $this->proxyPort     = null;
        $this->hasBeenInited = false;
        $this->isTrustAllServersEnabled = false;
        $this->httpClient    = new Client();
    }

    /**
     * Float describing the timeout of the request, client side, in seconds. Use 0 to wait indefinitely (the default behavior).
     *
     * @param float $timeout
     */
    public function setClientRequestTimeout($timeout)
    {
        $this->httpClient->setDefaultOption('timeout', $timeout);
    }

    /**
     * @return int
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * @param array $settings
     */
    public function init(array $settings)
    {
        if($this->hasBeenInitialized()) {
            return;
        }

        $this->createProxy();

        if (array_key_exists('remapHosts', $settings)) {
            $this->remapHosts($settings['remapHosts']);
        }

        if (array_key_exists('connectTimeout', $settings) && array_key_exists('readTimeout', $settings)) {
            $this->setTimeout($settings['connectTimeout'], $settings['readTimeout']);
        }

        if (array_key_exists('blacklist_urls', $settings)) {
            $this->setBlackListUrls($settings['blacklist_urls']);
        }
        $this->hasBeenInited = true;
    }

    /**
     * @param array $hostsMappings
     * @param array $hostnamePrefixes
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function remapHosts(array $hostsMappings, array $hostnamePrefixes = [ 'wwww', 'ssl'])
    {
        if (empty($hostsMappings)) {
            return null;
        }
        $mappings = [];
        foreach ($hostsMappings as $host => $ip) {
            $mappings[$host] = $ip;
            foreach ($hostnamePrefixes as $prefix) {
                $mappings[$prefix . '.' . $host] = $ip;
            }
        }

        $url = $this->makeUrl(sprintf(static::ENDPOINT_HOSTS, $this->proxyPort));
        return $this->httpClient->post($url, ['json' => $mappings]);
    }

    /**
     * @param int $connectTimeout
     * @param int $readTimeout
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function setTimeout($connectTimeout = 2000, $readTimeout = 2000)
    {
        $url = $this->makeUrl(sprintf(static::ENDPOINT_TIMEOUT, $this->proxyPort));
        return $this->httpClient->put(
            $url,
            [
                'body' => [
                    'connectTimeout' => $connectTimeout,
                    'readTimeout' => $readTimeout
                ]
            ]
        );
    }

    /**
     * @param $pageReference
     * @param bool|true $captureHeaders
     * @param bool|true $captureContent
     * @param bool|false $captureBinaryContent
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function startTrafficRecording($pageReference, $captureHeaders = true, $captureContent = true, $captureBinaryContent = false)
    {
        $url = $this->makeUrl(sprintf(static::ENDPOINT_HAR, $this->proxyPort));
        return $this->httpClient->put(
            $url,
            [
                'json' => [
                    'initialPageRef' => $pageReference,
                    'captureHeaders' => $captureHeaders,
                    'captureContent' => $captureContent,
                    'captureBinaryContent' => $captureBinaryContent
                ]
            ]
        );
    }

    /**
     * Retrieve HAR file from the proxy service.
     *
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function getHar()
    {
        $url = $this->makeUrl(sprintf(static::ENDPOINT_HAR, $this->proxyPort));
        return $this->httpClient->get($url)->getBody();
    }

    /**
     * Retrieve HAR file and decode it into a readable object.
     *
     * @return \StdClass
     */
    public function getRawHar()
    {
        return json_decode($this->getHar()->getContents());
    }

    /**
     * Sets custom page ref name
     *
     * @param string $pageTitle
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function setPageRef($pageTitle)
    {
        $url = $this->makeUrl(sprintf(static::ENDPOINT_PAGEREF, $this->proxyPort));
        $options = [
            'body' => [
                'pageTitle' => $pageTitle
            ]
        ];
        return $this->httpClient->put($url, $options);
    }

    /**
     * Retrieve a list of all requested urls by the browser.
     *
     * @return array
     */
    public function getAllRequestedUrls()
    {
        $rawHar = $this->getRawHar();

        $urlsList = [];
        foreach ($rawHar->log->entries as $harEntry) {
            $urlsList[] = $harEntry->request->url;
        }

        return $urlsList;
    }

    /**
     * @param array $urls
     * @throws \Exception
     */
    public function setBlackListUrls(array $urls)
    {
        foreach ($urls as $pattern => $responseCode) {
            $this->setBlackListUrl($pattern, $responseCode);
        }
    }

    /**
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function clearDnsCache()
    {
        $url = $this->makeUrl(sprintf(static::ENDPOINT_CACHE, $this->proxyPort));
        return $this->httpClient->delete($url);
    }

    /**
     * @param $pattern
     * @param $responseCode
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     * @throws \Exception
     */
    public function setBlackListUrl($pattern, $responseCode)
    {
        if (!is_integer($responseCode) || $responseCode < 100 || $responseCode >= 600) {
            throw new \Exception(
                "\$responseCode needs to be a valid HTTP integer number" .
                " (see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html)"
            );
        }

        $url     = $this->makeUrl(sprintf(static::ENDPOINT_BLACKLIST, $this->proxyPort));
        $options = [
            'body' => [
                'regex' => $pattern,
                'status' => $responseCode
            ]
        ];
        return $this->httpClient->put($url, $options);
    }

    /**
     * @return array
     */
    public function getProxiesList()
    {
        $proxyList = json_decode($this->httpClient->get($this->makeUrl(static::ENDPOINT_PROXY))->getBody(), true);
        return $proxyList['proxyList'];
    }

    /**
     * @param $port
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function deleteProxy($port)
    {
        return $this->httpClient->delete($this->makeUrl(static::ENDPOINT_PROXY . $port));
    }

    /**
     * @param array $proxies
     */
    public function deleteAllProxies(array $proxies)
    {
        foreach ($proxies as $proxy) {
            $this->deleteProxy($proxy['port']);
        }
    }

    /**
     * @return void
     */
    public function clearProxies()
    {
        $this->deleteAllProxies($this->getProxiesList());
    }

    /**
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function createProxy()
    {
        $url = $this->makeUrl(static::ENDPOINT_PROXY);

        if ( $this->isTrustAllServersEnabled()) {
            $url = sprintf("%s?trustAllServers=true", $url);
        }

        $futureResponse = $this->httpClient->post($url);
        $this->proxyPort = $futureResponse->json()['port'];
        return $futureResponse;
    }

    /**
     * @return ProxyAssertionsInterface
     */
    public function assertThat()
    {
        return new ProxyAssertions($this);
    }

    /**
     * @param $url
     * @return string
     */
    protected function makeUrl($url)
    {
        return sprintf('%s:%d%s', $this->url, $this->port, $url);
    }

    /**
     * @return boolean
     */
    public function hasBeenInitialized()
    {
        if ($this->hasBeenInited) {
            return true;
        }

        $proxyList = $this->getProxiesList();

        // check if this proxy was already initialized
        foreach ($proxyList as $key => $proxy) {
            // check if the current proxy exists
            if ($key == 'port' && $proxy['port'] == $this->proxyPort) {
                return true;
            }
        }
        // the proxy is not configured yet
        return false;
    }

    /**
     * @return void
     */
    public function enableTrustAllServers()
    {
        $this->isTrustAllServersEnabled = true;
    }

    /**
     * @return boolean
     */
    public function isTrustAllServersEnabled()
    {
        return $this->isTrustAllServersEnabled;
    }
}
