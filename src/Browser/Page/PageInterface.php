<?php
namespace Athena\Browser\Page;

use Athena\Browser\Page\Element\ElementSelector;
use Athena\Browser\Page\Find\Decorator\PageFinderWithWaits;
use Athena\Browser\Page\Find\Decorator\PageFinderWithAssertions;
use Athena\Browser\Page\Find\PageFinderInterface;
use Facebook\WebDriver\JavaScriptExecutor;

interface PageInterface extends JavaScriptExecutor
{
    /**
     * @return PageFinderInterface
     */
    public function find();

    /**
     * @return PageFinderWithAssertions
     */
    public function findAndAssertThat();

    /**
     * @param $timeOutInSeconds
     * @return PageFinderWithWaits
     */
    public function wait($timeOutInSeconds);

    /**
     * @return ElementSelector
     */
    public function getElement();
}

