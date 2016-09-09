<?php
namespace Athena\Browser;

use Athena\Browser\Page\PageInterface;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\JavaScriptExecutor;

interface BrowserInterface extends WebDriver, JavaScriptExecutor
{
    /**
     * @deprecated
     * @param $sessionId
     * @param string $path
     * @param bool|false $isSecure
     * @return BrowserInterface
     */
    public function withSession($sessionId, $path = "/", $isSecure = false);

    /**
     * @param $url
     * @return PageInterface
     */
    public function get($url);

    /**
     * @param $sessionId
     * @param string $path
     * @param bool|false $isSecure
     * @return void
     */
    public function setSession($sessionId, $path = "/", $isSecure = false);

    /**
     * @return void
     */
    public function deleteSession();

    /**
     * @return array
     */
    public function getSession();

    /**
     * @return void
     */
    public function deleteAllCookies();

    /**
     * @return bool
     */
    public function cleanup();

    /**
     * Get current page
     *
     * @return PageInterface
     */
    public function getCurrentPage();

    /**
     * @return \Athena\Translator\UrlTranslator
     */
    public function getUrlTranslator();

    /**
     * @return \Facebook\WebDriver\Remote\RemoteMouse
     */
    public function getMouse();
}

