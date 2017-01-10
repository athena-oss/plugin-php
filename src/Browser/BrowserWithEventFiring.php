<?php
namespace Athena\Browser;

use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use OLX\FluentWebDriverClient\Browser\Page\Page;
use OLX\FluentWebDriverClient\Browser\Page\PageInterface;
use Athena\Event\FindElementCompleted;
use Athena\Event\FindElementsCompleted;
use Athena\Event\NavigateToCompleted;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverNavigation;
use Facebook\WebDriver\WebDriverOptions;
use Facebook\WebDriver\WebDriverTargetLocator;
use Facebook\WebDriver\WebDriverWait;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BrowserWithEventFiring implements BrowserInterface
{
    /**
     * @var \OLX\FluentWebDriverClient\Browser\BrowserInterface
     */
    private $browser;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @inheritDoc
     */
    public function __construct(BrowserInterface $browser, EventDispatcher $dispatcher)
    {
        $this->browser = $browser;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $url
     *
     * @return PageInterface
     */
    public function get($url)
    {
        $this->navigate()->to($this->getUrlTranslator()->get($url));

        $this->dispatcher->dispatch(NavigateToCompleted::AFTER, new NavigateToCompleted($url, $this));

        return new Page($this);
    }

    /**
     * @deprecated
     * @param            $sessionId
     * @param string     $path
     * @param bool|false $isSecure
     *
     * @return BrowserInterface
     */
    public function withSession($sessionId, $path = "/", $isSecure = false)
    {
        $this->browser->withSession($sessionId, $path, $isSecure);

        return $this;
    }

    /**
     * @param            $sessionId
     * @param string     $path
     * @param bool|false $isSecure
     *
     * @return void
     */
    public function setSession($sessionId, $path = "/", $isSecure = false)
    {
        $this->browser->setSession($sessionId, $path, $isSecure);
    }

    /**
     * @return void
     */
    public function deleteSession()
    {
        $this->browser->deleteSession();
    }

    /**
     * @return array
     */
    public function getSession()
    {
        return $this->browser->getSession();
    }

    /**
     * @return void
     */
    public function deleteAllCookies()
    {
        $this->browser->deleteAllCookies();
    }

    /**
     * @return bool
     */
    public function cleanup()
    {
        return $this->browser->cleanup();
    }

    /**
     * Get current page
     *
     * @return PageInterface
     */
    public function getCurrentPage()
    {
        return new Page($this);
    }

    /**
     * @return \OLX\FluentWebDriverClient\Translator\UrlTranslator
     */
    public function getUrlTranslator()
    {
        return $this->browser->getUrlTranslator();
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteMouse
     */
    public function getMouse()
    {
        return $this->browser->getMouse();
    }

    /**
     * Close the current window.
     *
     * @return WebDriver The current instance.
     */
    public function close()
    {
        return $this->browser->close();
    }

    /**
     * Get a string representing the current URL that the browser is looking at.
     *
     * @return string The current URL.
     */
    public function getCurrentURL()
    {
        return $this->browser->getCurrentURL();
    }

    /**
     * Get the source of the last loaded page.
     *
     * @return string The current page source.
     */
    public function getPageSource()
    {
        return $this->browser->getPageSource();
    }

    /**
     * Get the title of the current page.
     *
     * @return string The title of the current page.
     */
    public function getTitle()
    {
        return $this->browser->getTitle();
    }

    /**
     * Return an opaque handle to this window that uniquely identifies it within
     * this driver instance.
     *
     * @return string The current window handle.
     */
    public function getWindowHandle()
    {
        return $this->browser->getWindowHandle();
    }

    /**
     * Get all window handles available to the current session.
     *
     * @return array An array of string containing all available window handles.
     */
    public function getWindowHandles()
    {
        return $this->browser->getWindowHandles();
    }

    /**
     * Quits this driver, closing every associated window.
     *
     * @return void
     */
    public function quit()
    {
        $this->browser->quit();
    }

    /**
     * Take a screenshot of the current page.
     *
     * @param string $save_as The path of the screenshot to be saved.
     *
     * @return string The screenshot in PNG format.
     */
    public function takeScreenshot($save_as = null)
    {
        return $this->browser->takeScreenshot($save_as);
    }

    /**
     * Construct a new WebDriverWait by the current WebDriver instance.
     * Sample usage:
     *
     *   $driver->wait(20, 1000)->until(
     *     WebDriverExpectedCondition::titleIs('WebDriver Page')
     *   );
     *
     * @param int $timeout_in_second
     * @param int $interval_in_millisecond
     *
     * @return WebDriverWait
     */
    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250)
    {
        return $this->browser->wait($timeout_in_second, $interval_in_millisecond);
    }

    /**
     * An abstraction for managing stuff you would do in a browser menu. For
     * example, adding and deleting cookies.
     *
     * @return WebDriverOptions
     */
    public function manage()
    {
        return $this->browser->manage();
    }

    /**
     * An abstraction allowing the driver to access the browser's history and to
     * navigate to a given URL.
     *
     * @return WebDriverNavigation
     * @see WebDriverNavigation
     */
    public function navigate()
    {
        return $this->browser->navigate();
    }

    /**
     * Switch to a different window or frame.
     *
     * @return WebDriverTargetLocator
     * @see WebDriverTargetLocator
     */
    public function switchTo()
    {
        return $this->browser->switchTo();
    }

    /**
     * @param string $name
     * @param array  $params
     *
     * @return mixed
     */
    public function execute($name, $params)
    {
        return $this->browser->execute($name, $params);
    }

    /**
     * Find the first WebDriverElement within this element using the given
     * mechanism.
     *
     * @param WebDriverBy $locator
     *
     * @return WebDriverElement NoSuchElementException is thrown in
     *    HttpCommandExecutor if no element is found.
     * @see WebDriverBy
     */
    public function findElement(WebDriverBy $locator)
    {
        $element = $this->browser->findElement($locator);

        $this->dispatcher->dispatch(FindElementCompleted::AFTER, new FindElementCompleted($locator, $this));

        return new ElementWithEventFiring($element, $this, $this->dispatcher);
    }

    /**
     * Find all WebDriverElements within this element using the given mechanism.
     *
     * @param WebDriverBy $locator
     *
     * @return WebDriverElement[] A list of all WebDriverElements, or an empty array if
     *    nothing matches
     * @see WebDriverBy
     */
    public function findElements(WebDriverBy $locator)
    {
        $elements = $this->browser->findElements($locator);

        $this->dispatcher->dispatch(FindElementsCompleted::AFTER, new FindElementsCompleted($locator, $this));

        return $elements;
    }

    /**
     * Inject a snippet of JavaScript into the page for execution in the context
     * of the currently selected frame. The executed script is assumed to be
     * synchronous and the result of evaluating the script will be returned.
     *
     * @param string $script    The script to inject.
     * @param array  $arguments The arguments of the script.
     *
     * @return mixed The return value of the script.
     */
    public function executeScript($script, array $arguments = [])
    {
        $this->browser->executeScript($script, $arguments);
    }

    /**
     * Inject a snippet of JavaScript into the page for asynchronous execution in
     * the context of the currently selected frame.
     *
     * The driver will pass a callback as the last argument to the snippet, and
     * block until the callback is invoked.
     *
     * @see WebDriverExecuteAsyncScriptTestCase
     *
     * @param string $script    The script to inject.
     * @param array  $arguments The arguments of the script.
     *
     * @return mixed The value passed by the script to the callback.
     */
    public function executeAsyncScript($script, array $arguments = [])
    {
        $this->browser->executeAsyncScript($script, $arguments);
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}

