<?php
namespace Athena\Browser\Page\Find\Wait;

use Athena\Exception\NoSuchElementException;

class WaitUntilPresence extends AbstractWait
{
    protected function validate($targetClosure, $locator = null)
    {
        if (sizeof($targetClosure()) === 0) {
            throw new NoSuchElementException(
                sprintf('Timeout waiting for the element to exist after %d seconds', $this->timeOutInSeconds)
            );
        }
        return true;
    }
}

