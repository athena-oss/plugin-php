<?php
namespace Athena\Browser\Page\Find\Decorator;

interface TargetDecoratorInterface
{
    public function decorate($targetClosure, $locator);
}

