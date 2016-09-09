<?php
namespace Athena\Browser\Page\Find\Assertion;

use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsAreHiddenAssertion extends AllElementsApplyToAssertion
{
    /**
     * AllElementsAreHiddenAssertion constructor.
     */
    public function __construct()
    {
        parent::__construct(function (RemoteWebElement $element) {
            return !$element->isDisplayed();
        }, 'hidden');
    }
}

