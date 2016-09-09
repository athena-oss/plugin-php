<?php
namespace Athena\Translator;

use Athena\Exception\InvalidUrlException;

class UrlTranslator
{
    const BASE_URL_IDENTIFIER = '/';

    /**
     * @var array
     */
    private $urlMappings;

    /**
     * @var array
     */
    private $fullUrls;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * UrlTranslator constructor.
     * @param array $urlMappings
     * @param string $baseUrl
     */
    public function __construct(array $urlMappings, $baseUrl)
    {
        $this->urlMappings                              = $urlMappings;
        $this->urlMappings[static::BASE_URL_IDENTIFIER] = $baseUrl;
        $this->baseUrl                                  = $baseUrl;
        $this->fullUrls                                 = [];
    }

    /**
     * @param $url
     * @return string
     */
    public function get($url)
    {
        // direct url
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $url;
        }

        // already stored
        if (array_key_exists($url, $this->fullUrls)) {
            return $this->fullUrls[$url];
        }

        // convert, store and return
        return $this->fullUrls[$url] = $this->getUrl($url);
    }

    /**
     * @param $url
     * @return string
     * @throws InvalidUrlException
     */
    private function getUrl($url)
    {
        if ($this->isBaseUrl($url)) {
            return $this->validateUrlAndReturn($this->baseUrl);
        }

        if ($this->isRelativeUrl($url)) {
            return $this->getRelativeUrl($url);
        }

        return $this->getUrlFromKey($url);
    }

    /**
     * @param $url
     *
     * @return bool
     * @throws InvalidUrlException
     */
    private function isBaseUrl($url)
    {
        if ($url === static::BASE_URL_IDENTIFIER
            || array_key_exists($url, $this->urlMappings) && $this->baseUrl === $this->urlMappings[$url]
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $url
     * @return bool
     */
    private function isRelativeUrl($url)
    {
        return preg_match('/^\//', $url) === 1;
    }

    /**
     * @param $url
     * @return mixed
     * @throws InvalidUrlException
     */
    private function getRelativeUrl($url)
    {
        $url = $this->baseUrl . $url;
        return $this->validateUrlAndReturn($url);
    }

    /**
     * @param $key
     * @return mixed
     * @throws InvalidUrlException
     */
    private function getUrlFromKey($key)
    {
        if (!array_key_exists($key, $this->urlMappings)) {
            throw new InvalidUrlException("URL is invalid '$key'");
        }

        $url = $this->baseUrl . $this->urlMappings[$key];
        return $this->validateUrlAndReturn($url);
    }

    /**
     * @param $url
     * @return mixed
     * @throws InvalidUrlException
     */
    private function validateUrlAndReturn($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException("URL is invalid '$url'");
        }
        return $url;
    }
}

