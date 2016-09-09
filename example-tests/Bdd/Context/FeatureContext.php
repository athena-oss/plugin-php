<?php

namespace Tests\Bdd\Context;

use Athena\Athena;
use Athena\Browser\BrowserInterface;
use Athena\Test\AthenaTestContext;
use PHPUnit_Framework_Assert;

/**
 * Features context.
 */
class FeatureContext extends AthenaTestContext
{
    /**
     * @var \Athena\Browser\Page\PageInterface
     */
    private $currentLocation;

    /**
     * @var BrowserInterface
     */
    private $browser;

    /**
     * @BeforeScenario
     */
    public function startup()
    {
        $this->browser = Athena::browser(true);
    }

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        $this->browser->cleanup();
    }

    /**
     * @Given /^the current location is the home page$/
     */
    public function theCurrentLocationIsTheHomePage()
    {
        $this->currentLocation = $this->browser->get('https://google.com/');
    }

    /**
     * @When /^the Anonymous User writes "([^"]*)" in the search box$/
     */
    public function theAnonymousUserWritesInTheSearchBox($arg1)
    {
        $this->currentLocation
            ->find()
            ->elementWithName('q')
            ->sendKeys($arg1);
    }

    /**
     * @Given /^the Anonymous User performs a click in the search button$/
     */
    public function theAnonymousUserPerformsAClickInTheSearchButton()
    {
        $this->currentLocation
            ->find()
            ->elementWithName('btnG')
            ->click();
    }

    /**
     * @Then /^the current location should be results page$/
     */
    public function theCurrentLocationShouldBeResultsPage()
    {
        PHPUnit_Framework_Assert::assertStringEndsWith("#q=athena", Athena::browser()->getCurrentURL());
    }
}