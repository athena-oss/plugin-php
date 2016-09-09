<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Exception\ElementIsVisibleException;
use Closure;

class ElementIsDisplayedAssertion extends AbstractElementAssertion
{
    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     * @throws \Athena\Exception\ElementIsVisibleException
     */
    public function assert(Closure $getElementClosure)
    {
        $element = $getElementClosure();

        if ($element->isDisplayed()) {
            throw new ElementIsVisibleException("Failed assertion that element is displayed.");
        }

        return $element;
    }
}

