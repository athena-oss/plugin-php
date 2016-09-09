<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Exception\ElementIsVisibleException;
use Athena\Exception\ElementNotEnabledException;
use Athena\Exception\ElementNotVisibleException;
use Closure;

class ElementIsEnabledAssertion extends AbstractElementAssertion
{
    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     * @throws \Athena\Exception\ElementNotEnabledException
     */
    public function assert(Closure $getElementClosure)
    {
        $element = $getElementClosure();

        if (!$element->isEnabled()) {
            throw new ElementNotEnabledException("Failed assertion that element is enabled.");
        }

        return $element;
    }
}

