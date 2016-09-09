<?php
namespace Athena\Browser\Page\Find\Wait;

use Athena\Exception\ElementNotExpectedException;

class WaitUntilAbsence extends AbstractWait
{
    protected function validate($targetClosure, $locator = null)
    {
        if (sizeof($targetClosure()) !== 0) {
            throw new ElementNotExpectedException(
                sprintf('Timeout waiting for the element to not exist after %d seconds', $this->timeOutInSeconds)
            );
        }
        return true;
    }
}

