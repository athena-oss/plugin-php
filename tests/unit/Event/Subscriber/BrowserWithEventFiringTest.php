<?php

namespace Athena\Tests\Event\Subscriber;

use OLX\FluentWebDriverClient\Browser\Browser;
use Athena\Browser\BrowserWithEventFiring;
use Athena\Event\ClickCompleted;
use Athena\Event\FindElementCompleted;
use Athena\Event\FindElementsCompleted;
use Athena\Event\NavigateToCompleted;
use Athena\Event\SendKeysCompleted;
use Athena\Translator\UrlTranslator;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverNavigation;
use Phake;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BrowserWithEventFiringTest extends \PHPUnit_Framework_TestCase
{
    private $fakeBrowser;
    private $fakeEventDispatcher;
    private $fakeBrowserWithEventFiring;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->fakeEventDispatcher  = Phake::mock(EventDispatcher::class);
        $this->fakeBrowser          = Phake::mock(Browser::class);
        $fakeNavigation             = Phake::mock(WebDriverNavigation::class);
        $fakeUrlTranslator          = Phake::mock(UrlTranslator::class);
        $fakeElement                = Phake::mock(WebDriverElement::class);

        Phake::when($this->fakeBrowser)->getUrlTranslator()->thenReturn($fakeUrlTranslator);
        Phake::when($this->fakeBrowser)->navigate()->thenReturn($fakeNavigation);
        Phake::when($this->fakeBrowser)->findElement(Phake::anyParameters())->thenReturn($fakeElement);

        $this->fakeBrowserWithEventFiring = new BrowserWithEventFiring($this->fakeBrowser, $this->fakeEventDispatcher);
    }

    public function testAfterNavigateTo_BrowserNavigatesToUrl_ShouldDispatchNavigateToCompletedEvent()
    {
        $this->fakeBrowserWithEventFiring->get('http://fakeurl.com');

        $eventName = null;
        $event     = null;

        Phake::verify($this->fakeEventDispatcher, Phake::times(1))->dispatch(Phake::capture($eventName), Phake::capture($event));

        $this->assertEquals($eventName, NavigateToCompleted::AFTER);
        $this->assertInstanceOf(NavigateToCompleted::class, $event);
        $this->assertEquals('http://fakeurl.com', $event->getUrl());
        $this->assertSame($this->fakeBrowserWithEventFiring, $event->getBrowser());
    }

    public function testAfterFindElement_FindElementInPage_ShouldDispatchFindElementCompleted()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);

        Phake::when($fakeWebDriverBy)->getMechanism()->thenReturn('name');
        Phake::when($fakeWebDriverBy)->getValue()->thenReturn('snape');

        $this->fakeBrowserWithEventFiring->findElement($fakeWebDriverBy);

        $eventName = null;
        $event     = null;

        Phake::verify($this->fakeEventDispatcher, Phake::times(1))->dispatch(Phake::capture($eventName), Phake::capture($event));

        $this->assertEquals($eventName, FindElementCompleted::AFTER);
        $this->assertInstanceOf(FindElementCompleted::class, $event);
        $this->assertEquals('name', $event->getLocator()->getMechanism());
        $this->assertEquals('snape', $event->getLocator()->getValue());
        $this->assertSame($this->fakeBrowserWithEventFiring, $event->getBrowser());
    }

    public function testAfterFindElements_FindElementInPage_ShouldDispatchFindElementCompleted()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);

        Phake::when($fakeWebDriverBy)->getMechanism()->thenReturn('name');
        Phake::when($fakeWebDriverBy)->getValue()->thenReturn('snape');

        $this->fakeBrowserWithEventFiring->findElements($fakeWebDriverBy);

        $eventName = null;
        $event     = null;

        Phake::verify($this->fakeEventDispatcher, Phake::times(1))->dispatch(Phake::capture($eventName), Phake::capture($event));

        $this->assertEquals($eventName, FindElementsCompleted::AFTER);
        $this->assertInstanceOf(FindElementsCompleted::class, $event);
        $this->assertEquals('name', $event->getLocator()->getMechanism());
        $this->assertEquals('snape', $event->getLocator()->getValue());
        $this->assertSame($this->fakeBrowserWithEventFiring, $event->getBrowser());
    }

    public function testAfterSendKeys_ChangeElementValue_ShouldDispatchSendKeysCompleted()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);

        $this->fakeBrowserWithEventFiring
            ->findElement($fakeWebDriverBy)
            ->sendKeys('fake text');

        $eventName = null;
        $event     = null;

        Phake::verify($this->fakeEventDispatcher, Phake::times(2))->dispatch(Phake::capture($eventName), Phake::capture($event));

        $this->assertEquals($eventName, SendKeysCompleted::AFTER);
        $this->assertInstanceOf(SendKeysCompleted::class, $event);
        $this->assertEquals('fake text', $event->getValue());
        $this->assertSame($this->fakeBrowserWithEventFiring, $event->getBrowser());
    }

    public function testAfterClick_ClickedElement_ShouldDispatchClickCompleted()
    {
        $fakeWebDriverBy = Phake::mock(WebDriverBy::class);

        $this->fakeBrowserWithEventFiring
            ->findElement($fakeWebDriverBy)
            ->click();

        $eventName = null;
        $event     = null;

        Phake::verify($this->fakeEventDispatcher, Phake::times(2))->dispatch(Phake::capture($eventName), Phake::capture($event));

        $this->assertEquals($eventName, ClickCompleted::AFTER);
        $this->assertInstanceOf(ClickCompleted::class, $event);
        $this->assertSame($this->fakeBrowserWithEventFiring, $event->getBrowser());
    }
}
