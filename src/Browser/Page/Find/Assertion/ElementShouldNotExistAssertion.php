<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\ElementNotExpectedException;
use Athena\Exception\StopChainException;
use Facebook\WebDriver\Exception\NoSuchElementException;

class ElementShouldNotExistAssertion implements TargetDecoratorInterface
{
    public function decorate($targetClosure, $locator)
    {
        try {
            if ($count = sizeof($targetClosure()) > 0) {
                throw new ElementNotExpectedException(
                    sprintf("Expected element should not exist on the page and was found '%d' time(s)", $count)
                );
            }
        } catch (NoSuchElementException $e) {
            throw new StopChainException();
        }
    }
}

