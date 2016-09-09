<?php
namespace Tests;

use Athena\Athena;
use Athena\Test\AthenaBrowserTestCase;
use Tests\Browser\Page\GooglePage;

class DummyBrowserTest extends AthenaBrowserTestCase
{
    /**
     * @var \Athena\Browser\BrowserInterface
     */
    private $browser;

    protected function setUp()
    {
        $this->browser = Athena::browser(true);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        if (!is_null($this->browser)) {
            Athena::getInstance()->setBrowser(null);
            $this->browser->cleanup();
        }
    }

    public function testGoogle_NoQueryStringIsProvidedAndSearchButtonIsClicked_ShouldShowResultsPage()
    {
        $this->browser->get("http://google.pt")
            ->find()
            ->elementWithName('btnI')
            ->click();
    }

    public function testGoogle_FeelingLuckyButtonExists_ValueShouldBeCorrect()
    {
        $this->browser->get("http://google.pt")
            ->findAndAssertThat()
            ->valueEquals("Sinto-me com sorte")
            ->elementWithName('btnI');
    }

    public function testGoogle_QueryStringIsWritten_ShouldShowResultsPage()
    {
        $googlePage = new GooglePage();
        $googlePage->open()->searchFor('teste');
    }

    public function testGoogle_WaitForElementExistenceAndClick_ShouldShowResultsPage()
    {
        $this->browser->get("http://google.pt")
            ->getElement()
            ->withName('btnI')
            ->wait(1)
            ->toBePresent()
            ->thenFind()
            ->asHtmlElement()
            ->click();
    }
}
