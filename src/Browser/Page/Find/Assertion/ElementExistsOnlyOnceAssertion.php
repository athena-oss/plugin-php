<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;

class ElementExistsOnlyOnceAssertion implements TargetDecoratorInterface
{
    const EXPECTED_COUNT = 1;

    public function decorate($targetClosure, $locator)
    {
        if ($count = sizeof($targetClosure()) !== static::EXPECTED_COUNT) {
            throw new \Exception(
                sprintf("Expected element count is different than expected was %d and found %d", self::EXPECTED_COUNT, $count)
            );
        }
        return true;
    }
}

