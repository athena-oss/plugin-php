<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Exception\ElementNotExpectedException;
use Athena\Exception\NoSuchElementException;
use Athena\Exception\StopChainException;
use Closure;

class ElementDoesNotExistAssertion extends AbstractElementAssertion
{
    /**
     * @param \Closure $elementReturnClojure
     *
     * @return mixed
     * @throws \Athena\Exception\ElementNotExpectedException
     * @throws \Athena\Exception\StopChainException
     *
     */
    public function assert(Closure $elementReturnClojure)
    {
        try {
            $element = $elementReturnClojure();

            if ($count = sizeof($element) > 0) {
                throw new ElementNotExpectedException(
                    sprintf("Expected element should not exist on the page and was found '%d' time(s)", $count)
                );
            }

            return $element;
        } catch (NoSuchElementException $e) {
            throw new StopChainException();
        }
    }
}

