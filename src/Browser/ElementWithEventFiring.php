<?php
namespace Athena\Browser;

use Athena\Event\ClickCompleted;
use Athena\Event\SendKeysCompleted;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Internal\WebDriverLocatable;
use Facebook\WebDriver\Remote\FileDetector;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverPoint;
use OLX\FluentWebDriverClient\Browser\BrowserInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ElementWithEventFiring implements WebDriverElement, WebDriverLocatable
{
    /**
     * @var \Facebook\WebDriver\WebDriverElement|\Facebook\WebDriver\Internal\WebDriverLocatable
     */
    private $element;
    /**
     * @var \OLX\FluentWebDriverClient\Browser\BrowserInterface
     */
    private $browser;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * ElementWithEventFiring constructor.
     *
     * @param \Facebook\WebDriver\WebDriverElement               $element
     * @param \OLX\FluentWebDriverClient\Browser\BrowserInterface                   $browser
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(WebDriverElement $element, BrowserInterface $browser, EventDispatcher $dispatcher)
    {
        $this->element = $element;
        $this->browser = $browser;
        $this->dispatcher = $dispatcher;
    }

    /**
     * If this element is a TEXTAREA or text INPUT element, this will clear the
     * value.
     *
     * @return WebDriverElement The current instance.
     */
    public function clear()
    {
        $this->element->clear();

        return $this;
    }

    /**
     * Click this element.
     *
     * @return WebDriverElement The current instance.
     */
    public function click()
    {
        $this->element->click();

        $this->dispatcher->dispatch(ClickCompleted::AFTER, new ClickCompleted($this->browser));

        return $this;
    }

    /**
     * Get the value of a the given attribute of the element.
     *
     * @param string $attribute_name The name of the attribute.
     *
     * @return string The value of the attribute.
     */
    public function getAttribute($attribute_name)
    {
        return $this->element->getAttribute($attribute_name);
    }

    /**
     * Get the value of a given CSS property.
     *
     * @param string $css_property_name The name of the CSS property.
     *
     * @return string The value of the CSS property.
     */
    public function getCSSValue($css_property_name)
    {
        return $this->element->getCSSValue($css_property_name);
    }

    /**
     * Get the location of element relative to the top-left corner of the page.
     *
     * @return WebDriverPoint The location of the element.
     */
    public function getLocation()
    {
        return $this->element->getLocation();
    }

    /**
     * Try scrolling the element into the view port and return the location of
     * element relative to the top-left corner of the page afterwards.
     *
     * @return WebDriverPoint The location of the element.
     */
    public function getLocationOnScreenOnceScrolledIntoView()
    {
        return $this->element->getLocationOnScreenOnceScrolledIntoView();
    }

    /**
     * Get the size of element.
     *
     * @return WebDriverDimension The dimension of the element.
     */
    public function getSize()
    {
        return $this->element->getSize();
    }

    /**
     * Get the tag name of this element.
     *
     * @return string The tag name.
     */
    public function getTagName()
    {
        return $this->element->getTagName();
    }

    /**
     * Get the visible (i.e. not hidden by CSS) innerText of this element,
     * including sub-elements, without any leading or trailing whitespace.
     *
     * @return string The visible innerText of this element.
     */
    public function getText()
    {
        return $this->element->getText();
    }

    /**
     * Is this element displayed or not? This method avoids the problem of having
     * to parse an element's "style" attribute.
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->element->isDisplayed();
    }

    /**
     * Is the element currently enabled or not? This will generally return true
     * for everything but disabled input elements.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->element->isEnabled();
    }

    /**
     * Determine whether or not this element is selected or not.
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->element->isSelected();
    }

    /**
     * Simulate typing into an element, which may set its value.
     *
     * @param mixed $value The data to be typed.
     *
     * @return WebDriverElement The current instance.
     */
    public function sendKeys($value)
    {
        $this->element->sendKeys($value);

        $this->dispatcher->dispatch(SendKeysCompleted::AFTER, new SendKeysCompleted($value, $this->browser));

        return $this;
    }

    /**
     * If this current element is a form, or an element within a form, then this
     * will be submitted to the remote server.
     *
     * @return WebDriverElement The current instance.
     */
    public function submit()
    {
        $this->element->submit();

        return $this;
    }

    /**
     * Get the opaque ID of the element.
     *
     * @return string The opaque ID.
     */
    public function getID()
    {
        return $this->element->getID();
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
        return new static($this->element->findElement($locator), $this->browser, $this->dispatcher);
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
        return $this->element->findElements($locator);
    }

    /**
     * @return WebDriverCoordinates
     */
    public function getCoordinates()
    {
        return $this->element->getCoordinates();
    }

    /**
     * Set the fileDetector in order to let the RemoteWebElement to know that
     * you are going to upload a file.
     *
     * Basically, if you want WebDriver trying to send a file, set the fileDetector
     * to be LocalFileDetector. Otherwise, keep it UselessFileDetector.
     *
     *   eg. $element->setFileDetector(new LocalFileDetector);
     *
     * @param FileDetector $detector
     * @return ElementWithEventFiring
     * @see FileDetector
     * @see LocalFileDetector
     * @see UselessFileDetector
     */
    public function setFileDetector(FileDetector $detector) {
        if ($this->element instanceof RemoteWebElement) {
            $this->element->setFileDetector($detector);
        }
        return $this;
    }
}

