<?php
namespace Athena\Browser\Page\Element\Find;

use Facebook\WebDriver\WebDriverExpectedCondition;

class ElementFindWithWait
{
    private $timeInSeconds;

    /**
     * @var \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    private $elementFinder;

    /**
     * ElementFindWithWait constructor.
     *
     * @param                                                          $timeInSeconds
     * @param \Athena\Browser\Page\Element\Find\ElementFinderInterface $elementFinder
     */
    public function __construct($timeInSeconds, ElementFinderInterface $elementFinder)
    {
        $this->timeInSeconds = $timeInSeconds;
        $this->elementFinder = $elementFinder;
    }

    /**
     * @return $this
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws null
     */
    public function toBePresent()
    {
        $this->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated($this->elementFinder->getSearchCriteria()));

        return $this;
    }

    /**
     * @return mixed
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws null
     */
    public function toBeVisible()
    {
        $this->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated($this->elementFinder->getSearchCriteria()));

        return $this;
    }

    /**
     * @return $this
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws null
     */
    public function toBeInvisible()
    {
        $this->wait()->until(WebDriverExpectedCondition::invisibilityOfElementLocated($this->elementFinder->getSearchCriteria()));

        return $this;
    }

    /**
     * @return $this
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws null
     */
    public function toBeClickable()
    {
        $this->wait()->until(WebDriverExpectedCondition::elementToBeClickable($this->elementFinder->getSearchCriteria()));

        return $this;
    }

    /**
     * @return $this
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws null
     */
    public function toBeSelectable()
    {
        $this->wait()->until(WebDriverExpectedCondition::elementToBeSelected($this->elementFinder->getSearchCriteria()));

        return $this;
    }

    /**
     * @return \Facebook\WebDriver\WebDriverWait
     */
    private function wait()
    {
        return $this->elementFinder->getBrowser()->wait($this->timeInSeconds, 250);
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    public function thenFind()
    {
        return $this->elementFinder;
    }
}

