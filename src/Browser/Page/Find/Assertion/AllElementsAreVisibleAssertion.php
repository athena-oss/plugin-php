<?php
namespace Athena\Browser\Page\Find\Assertion;

use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsAreVisibleAssertion extends AllElementsApplyToAssertion
{
    /**
     * AllElementsAreVisibleAssertion constructor.
     */
    public function __construct()
    {
        parent::__construct(function (RemoteWebElement $element) {
            return $element->isDisplayed();
        }, 'visible');
    }
}

