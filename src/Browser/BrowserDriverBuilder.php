<?php
namespace Athena\Browser;

use Athena\Configuration\Settings;
use Athena\Exception\UnsupportedBrowserException;
use Athena\Translator\UrlTranslator;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class BrowserDriverBuilder
{
    /**
     * @var RemoteWebDriver
     */
    private $remoteWebDriver;

    /**
     * @var UrlTranslator
     */
    private $urlTranslator;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $urls;

    /**
     * @var array
     */
    private $extraCapabilities;

    /**
     * @var int
     */
    private $implicitTimeout;

    /**
     * @var int
     */
    private $connectionTimeout;

    /**
     * @var int
     */
    private $requestTimeout;

    /**
     * @var array
     */
    private $chromeOptions;

    /**
     * @param \Athena\Configuration\Settings $settings
     *
     * @return $this
     * @throws \Athena\Exception\SettingNotFoundException
     */
    public static function fromSettings(Settings $settings)
    {
        $seleniumHubUrl  = $settings->getByPath('selenium.hub_url')->orFail();
        $implicitTimeout = $settings->getByPath('selenium.implicit_timeout')->orDefaultTo(0);
        $connectionTimeout = $settings->getByPath('selenium.connection_timeout')->orDefaultTo(null);
        $requestTimeout = $settings->getByPath('selenium.request_timeout')->orDefaultTo(null);
        $extraCapabilities = $settings->getByPath('selenium.browser.capabilities')->orDefaultTo([]);
        $chromeOptions = $settings->getByPath('selenium.chrome_options.arguments')->orDefaultTo([]);


        $builder = (new BrowserDriverBuilder($seleniumHubUrl))
            ->withType($settings->get('browser')->orFail())
            ->withProxySettings($settings->get('proxy')->orDefaultTo([]))
            ->withImplicitTimeout($implicitTimeout)
            ->withConnectionTimeout($connectionTimeout)
            ->withRequestTimeout($requestTimeout)
            ->withExtraCapabilities($extraCapabilities)
            ->withUrls($settings->get('urls')->orDefaultTo([]))
            ->withChromeOptions($chromeOptions);

        return $builder;

    }

    /**
     * BrowserBuilder constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->extraCapabilities = [];
        $this->urls = [];
    }


    public function __destruct()
    {
        $this->remoteWebDriver = null;
    }

    /**
     * @param $options
     * @return $this
     */
    public function withChromeOptions($options){
        $this->chromeOptions = $options;
        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function withType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param array $proxySettings
     *
     * @return $this
     */
    public function withProxySettings($proxySettings)
    {
        if (!empty($proxySettings)) {
            $this->extraCapabilities[WebDriverCapabilityType::PROXY] = [
                'proxyType' => $proxySettings['proxyType'],
                'httpProxy' => $proxySettings['httpProxy'],
                'sslProxy' => $proxySettings['sslProxy'],
            ];
        }
        return $this;
    }

    /**
     * @param $urls
     *
     * @return $this
     */
    public function withUrls(array $urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function withImplicitTimeout($timeout)
    {
        $this->implicitTimeout = $timeout;
        return $this;
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function withConnectionTimeout($seconds)
    {
        $this->connectionTimeout = $seconds ? $seconds * 1000 : null;
        return $this;
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function withRequestTimeout($seconds)
    {
        $this->requestTimeout = $seconds ? $seconds * 1000 : null;
        return $this;
    }

    /**
     * @param array $capabilities
     *
     * @return $this
     */
    public function withExtraCapabilities(array $capabilities)
    {
        $this->extraCapabilities = array_merge($this->extraCapabilities, $capabilities);
        return $this;
    }

    /**
     * @return BrowserDriverBuilder
     * @throws UnsupportedBrowserException
     */
    public function build()
    {

        $capabilities = $this->makeCapabilities($this->type, $this->extraCapabilities);

        $this->remoteWebDriver = RemoteWebDriver::create(
            $this->url,
            $capabilities,
            $this->connectionTimeout,
            $this->requestTimeout
        );

        // define web driver configurations before being decorated
        if ($this->implicitTimeout > 0) {
            $this->remoteWebDriver->manage()->timeouts()->implicitlyWait($this->implicitTimeout);
        }

        // translator
        $baseUrlId = UrlTranslator::BASE_URL_IDENTIFIER;
        $baseUrl = array_key_exists($baseUrlId, $this->urls) ? $this->urls[$baseUrlId] : null;
        $this->urlTranslator = new UrlTranslator($this->urls, $baseUrl);

        return $this;
    }

    /**
     * @return RemoteWebDriver
     */
    public function getRemoteWebDriver()
    {
        return $this->remoteWebDriver;
    }

    /**
     * @return UrlTranslator
     */
    public function getUrlTranslator()
    {
        return $this->urlTranslator;
    }

    /**
     * @param string $browserType
     * @param array  $desiredCapabilities
     *
     * @return array
     * @throws \Athena\Exception\UnsupportedBrowserException
     */
    private function makeCapabilities($browserType, $desiredCapabilities = [])
    {

        switch ($browserType) {
            case 'chrome':
                $chromeCaps = DesiredCapabilities::chrome();

                $option = new ChromeOptions();
                $option->addArguments($this->chromeOptions);
                $chromeCaps->setCapability(ChromeOptions::CAPABILITY,$option);

                return array_merge(
                    $chromeCaps->toArray(),$desiredCapabilities
                );
            case 'firefox':
                return array_merge(
                    DesiredCapabilities::firefox()->toArray(),
                    $desiredCapabilities
                );
            case 'phantomjs':
                return array_merge(
                    DesiredCapabilities::phantomjs()->toArray(),
                    $desiredCapabilities
                );
            default:
                throw new UnsupportedBrowserException("Browser not supported '$browserType'");
        }
    }
}

