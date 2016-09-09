<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\NoSuchElementException;

class ElementExistsAtLeastOnceAssertion implements TargetDecoratorInterface
{
    public function decorate($targetClosure, $locator)
    {
        $count = sizeof($targetClosure());
        if ($count === 0) {
            throw new NoSuchElementException("Expected element was not found on the page at least once");
        }
        return true;
    }
}

