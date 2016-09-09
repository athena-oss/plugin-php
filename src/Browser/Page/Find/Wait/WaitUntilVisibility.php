<?php
namespace Athena\Browser\Page\Find\Wait;

use Athena\Exception\ElementNotVisibleException;
use Facebook\WebDriver\WebDriverExpectedCondition;

class WaitUntilVisibility extends AbstractWait
{
    protected function validate($targetClosure, $locator = null)
    {
        try {
            WebDriverExpectedCondition::visibilityOf($targetClosure());
            return true;
        } catch (\Exception $e) {
            throw new ElementNotVisibleException(
                sprintf('Timeout waiting for the element to be visible after %d seconds', $this->timeOutInSeconds)
            );
        }
    }
}

