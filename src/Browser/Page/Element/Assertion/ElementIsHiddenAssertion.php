<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Exception\ElementIsVisibleException;
use Athena\Exception\ElementNotVisibleException;
use Closure;

class ElementIsHiddenAssertion extends AbstractElementAssertion
{
    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     * @throws \Athena\Exception\ElementNotVisibleException
     */
    public function assert(Closure $getElementClosure)
    {
        $element = $getElementClosure();

        if (!$element->isDisplayed()) {
            throw new ElementNotVisibleException("Failed assertion that element is visible.");
        }

        return $element;
    }
}

