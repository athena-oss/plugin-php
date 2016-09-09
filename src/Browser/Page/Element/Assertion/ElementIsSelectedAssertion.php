<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Exception\ElementIsVisibleException;
use Athena\Exception\ElementNotSelectedException;
use Athena\Exception\ElementNotVisibleException;
use Closure;

class ElementIsSelectedAssertion extends AbstractElementAssertion
{
    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     * @throws \Athena\Exception\ElementNotSelectedException
     */
    public function assert(Closure $getElementClosure)
    {
        $element = $getElementClosure();

        if (!$element->isSelected()) {
            throw new ElementNotSelectedException("Failed assertion that element is selected.");
        }

        return $element;
    }
}

