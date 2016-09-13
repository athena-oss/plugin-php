<?php
namespace Athena;

use Athena\Api\ApiClientBuilder;
use Athena\Browser\Browser;
use Athena\Browser\BrowserDriverBuilder;
use Athena\Browser\BrowserInterface;
use Athena\Browser\BrowserWithEventFiring;
use Athena\Configuration\Settings;
use Athena\Configuration\SettingsFactory;
use Athena\Event\Dispatcher\DispatcherRegistry;
use Athena\Info\AthenaInfo;
use Athena\Proxy\BrowserProxyClient;
use Athena\Translator\UrlTranslator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OLX\FluentHttpClient\HttpClientInterface;

class Athena
{
    /**
     * @var Athena
     */
    private static $instance;
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var BrowserInterface
     */
    private $browser;
    /**
     * @var HttpClientInterface
     */
    private $apiClient;
    /**
     * @var AthenaInfo
     */
    private $info;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var BrowserProxyClient
     */
    private $proxy;

    /**
     * @var UrlTranslator
     */
    private $urlTranslator;

    /**
     * @return Athena
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            $settings = null;

            if (defined('ATHENA_CONFIGURATION_FILE') && ATHENA_CONFIGURATION_FILE !== false) {
                $settings = SettingsFactory::fromJsonFile(ATHENA_CONFIGURATION_FILE);
            } else {
                $settings = SettingsFactory::fromArray([]);
            }

            $settings->set('athena_tests_exec_timer', ATHENA_START_TIMER);
            $settings->set('athena_tests_type', ATHENA_TESTS_TYPE);
            $settings->set('athena_tests_directory', ATHENA_TESTS_DIRECTORY);

            if (defined('ATHENA_BROWSER')) {
                $settings->set('browser', ATHENA_BROWSER);
            }

            static::$instance = new static($settings, new EventDispatcher());
        }

        return static::$instance;
    }

    /**
     * Athena constructor.
     *
     * @param \Athena\Configuration\Settings                              $settings
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    private function __construct(Settings $settings, EventDispatcherInterface $eventDispatcher)
    {
        $this->settings = $settings;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->browser         = null;
        $this->proxy           = null;
        $this->apiClient       = null;
        $this->info            = null;
        $this->eventDispatcher = null;
    }

    /**
     * @return UrlTranslator
     */
    public function getUrlTranslator()
    {
        return $this->urlTranslator;
    }

    /**
     * @param UrlTranslator $urlTranslator
     */
    public function setUrlTranslator($urlTranslator)
    {
        $this->urlTranslator = $urlTranslator;
    }

    public function registerSubscribers()
    {
        DispatcherRegistry::registerSubscriberFromSettings($this->eventDispatcher, $this->settings);
    }

    /**
     * @param bool $reset
     *
     * @return BrowserInterface
     * @throws Exception\SettingNotFoundException
     */
    public static function browser($reset = false)
    {
        $athena  = static::getInstance();
        if ($reset) {
            $athena->setBrowser(null);
        } else if (!is_null($browser = $athena->getBrowser())) {
            return $browser;
        }

        $athena->initProxyIfRequired();
        $driverBuilder = BrowserDriverBuilder::fromSettings($athena->settings());

        $browser = new Browser($driverBuilder);

        if ($athena->settings()->exists('report')) {
            $browser = new BrowserWithEventFiring($browser, $athena->getEventDispatcher());
        }

        $athena->setBrowser($browser);

        return $browser;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @return Settings
     */
    public static function settings()
    {
        return static::getInstance()->getSettings();
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param BrowserInterface $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param bool $mustHaveProxy
     *
     * @return BrowserProxyClient|null
     * @throws Exception\SettingNotFoundException
     */
    protected function initProxyIfRequired($mustHaveProxy = false)
    {
        if(!is_null($this->getProxy())) {
            return ;
        }

        if ($mustHaveProxy) {
            $proxySettings = $this->settings->get('proxy')->orFail();
        } else {
            $proxySettings = $this->settings->get('proxy')->orDefaultTo([]);
        }

        if (!empty($proxySettings)) {
            $proxySettings = $this->settings->get('proxy')->orDefaultTo([]);

            $proxy = new BrowserProxyClient(
                $proxySettings['url'],
                $proxySettings['port']
            );

            if (array_key_exists('timeout', $proxySettings)) {
                $proxy->setClientRequestTimeout($proxySettings['timeout']);
            }

            $initAlways = $this->settings->getByPath('proxy.init_always')->orDefaultTo(false);

            if (!$proxy->hasBeenInitialized() || $initAlways) {
                $proxy->init($proxySettings);
                $proxy->clearDnsCache();
            }

            $proxySettings['internalPort'] = $proxy->getProxyPort();
            $proxySettings['proxyType']    = "manual";
            $proxySettings['httpProxy']    = "athena-proxy:" .  $proxySettings['internalPort'];
            $proxySettings['sslProxy']     = "athena-proxy:" .  $proxySettings['internalPort'];
            $this->settings->set('proxy', $proxySettings);

            $recordTraffic = array_key_exists('recording', $proxySettings) && $proxySettings['recording'];
            if ($recordTraffic) {
                $proxy->startTrafficRecording('/');
            }

            $this->setProxy($proxy);
            return $proxy;
        }
        return null;
    }

    /**
     * @param BrowserProxyClient $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return HttpClientInterface
     */
    public static function api()
    {
        $athena = static::getInstance();
        if (!is_null($apiClient = $athena->getApiClient())) {
            return $apiClient;
        }

        $athena->initProxyIfRequired();

        $apiClient = (new ApiClientBuilder())
            ->withUrls($athena->settings()->get('urls')->orDefaultTo([]))
            ->withProxy($athena->settings()->get('proxy')->orDefaultTo([]))
            ->withHttpExceptions($athena->settings()->get('http_exceptions')->orDefaultTo(false));

        if ($athena->settings()->exists('report')) {
            $apiClient->withEventDispatcher($athena->getEventDispatcher());
        }

        $apiClient = $apiClient->build();

        $athena->setApiClient($apiClient);

        return $apiClient;
    }

    /**
     * @return UrlTranslator
     */
    public static function urls()
    {
        $athena = static::getInstance();
        if (!is_null($urlTranslator = $athena->getUrlTranslator())) {
            return $urlTranslator;
        }

        $urls   = $athena->settings()->get('urls')->orDefaultTo([]);
        $baseUrlId     = UrlTranslator::BASE_URL_IDENTIFIER;
        $baseUrl       = array_key_exists($baseUrlId, $urls) ? $urls[$baseUrlId] : null;
        $urlTranslator = new UrlTranslator($urls, $baseUrl);
        $athena->setUrlTranslator($urlTranslator);
        return $urlTranslator;
    }

    /**
     * @return HttpClientInterface
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param HttpClientInterface $apiClient
     */
    public function setApiClient(HttpClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**********************************************
     * Static stuff
     *********************************************/

    /**
     * @return AthenaInfo
     */
    public static function info()
    {
        return static::getInstance()->getInfo();
    }

    /**
     * @return AthenaInfo
     */
    public function getInfo()
    {
        if (is_null($this->info)) {
            $this->info = new AthenaInfo();
        }

        return $this->info;
    }

    /**
     * @return BrowserProxyClient
     */
    public static function proxy()
    {
        $athena = static::getInstance();
        if (!is_null($proxy = $athena->getProxy())) {
            return $proxy;
        }

        return $athena->initProxyIfRequired(true);
    }

    /**
     * @return BrowserProxyClient
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function getTestsType()
    {
        return $this->settings()->get('athena_tests_type')->orFail();
    }

    /**
     * @return string
     */
    public function getTestsDirectory()
    {
        return $this->settings()->get('athena_tests_directory')->orFail();
    }
}

