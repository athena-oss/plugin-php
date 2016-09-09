<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Browser\Page\Element\Find\ElementFinderInterface;
use Closure;

abstract class AbstractElementAssertion implements ElementFinderInterface
{
    /**
     * @var \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    private $elementFinder;

    /**
     * AbstractElementAssertion constructor.
     *
     * @param \Athena\Browser\Page\Element\Find\ElementFinderInterface $elementFinder
     */
    public function __construct(ElementFinderInterface $elementFinder)
    {
        $this->elementFinder = $elementFinder;
    }

    /**
     * @return \Facebook\WebDriver\WebDriverSelect
     * @throws \Athena\Exception\ElementNotExpectedException
     * @throws \Athena\Exception\StopChainException
     */
    public function asDropDown()
    {
        return $this->assert(function () {
            return $this->elementFinder->asDropDown();
        });
    }

    /**
     * @return \Facebook\WebDriver\Support\Events\EventFiringWebElement
     */
    public function asHtmlElement()
    {
        return $this->assert(function () {
            return $this->elementFinder->asHtmlElement();
        });
    }

    /**
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function getBrowser()
    {
        return $this->elementFinder->getBrowser();
    }

    /**
     * @return \Facebook\WebDriver\WebDriverBy
     */
    public function getSearchCriteria()
    {
        return $this->elementFinder->getSearchCriteria();
    }


    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     */
    abstract public function assert(Closure $getElementClosure);
}

