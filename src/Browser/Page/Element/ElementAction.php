<?php
namespace Athena\Browser\Page\Element;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Element\Find\ElementFind;
use Athena\Browser\Page\Element\Find\ElementFindWithAssertions;
use Athena\Browser\Page\Element\Find\ElementFindWithWait;
use Facebook\WebDriver\WebDriverBy;

class ElementAction
{
    /**
     * @var \Facebook\WebDriver\WebDriverBy
     */
    private $findBy;
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $browser;

    /**
     * ElementTypeBuilder constructor.
     *
     * @param \Facebook\WebDriver\WebDriverBy  $findBy
     * @param \Athena\Browser\BrowserInterface $browser
     */
    public function __construct(WebDriverBy $findBy, BrowserInterface $browser)
    {
        $this->findBy = $findBy;
        $this->browser = $browser;
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    public function thenFind()
    {
        return new ElementFind($this->findBy, $this->browser);
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFindWithAssertions
     */
    public function assertThat()
    {
        return new ElementFindWithAssertions($this->thenFind());
    }

    /**
     * @param $timeInSeconds
     *
     * @return \Athena\Browser\Page\Element\Find\ElementFindWithWait
     */
    public function wait($timeInSeconds)
    {
        return new ElementFindWithWait($timeInSeconds, $this->thenFind());
    }
}

