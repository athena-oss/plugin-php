<?php

namespace Athena\Browser;

use Athena\Configuration\Settings;
use OLX\FluentWebDriverClient\Browser\BrowserDriverBuilder;

class AthenaBrowserDriverBuilder
{
    /**
     * @param \Athena\Configuration\Settings $settings
     *
     * @return BrowserDriverBuilder
     * @throws \Athena\Exception\SettingNotFoundException
     */
    public static function fromAthenaSettings(Settings $settings)
    {
        $seleniumHubUrl  = $settings->getByPath('selenium.hub_url')->orFail();
        $implicitTimeout = $settings->getByPath('selenium.implicit_timeout')->orDefaultTo(0);
        $connectionTimeout = $settings->getByPath('selenium.connection_timeout')->orDefaultTo(null);
        $requestTimeout = $settings->getByPath('selenium.request_timeout')->orDefaultTo(null);
        $extraCapabilities = $settings->getByPath('selenium.browser.capabilities')->orDefaultTo([]);
        $chromeOptionsArguments = $settings->getByPath('selenium.chrome_options.arguments')->orDefaultTo([]);

        $builder = (new BrowserDriverBuilder($seleniumHubUrl))
            ->withType($settings->get('browser')->orFail())
            ->withProxySettings($settings->get('proxy')->orDefaultTo([]))
            ->withImplicitTimeout($implicitTimeout)
            ->withConnectionTimeout($connectionTimeout)
            ->withRequestTimeout($requestTimeout)
            ->withExtraCapabilities($extraCapabilities)
            ->withUrls($settings->get('urls')->orDefaultTo([]))
            ->withChromeOptionsArguments($chromeOptionsArguments);
        return $builder;
    }
}
