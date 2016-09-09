<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\UnexpectedValueException;

class ElementTextEqualsAssertion implements TargetDecoratorInterface
{
    private $expectedText;

    /**
     * TextEqualsAssertion constructor.
     * @param $compareToText
     */
    public function __construct($compareToText)
    {
        $this->expectedText = $compareToText;
    }

    public function decorate($targetClosure, $locator)
    {
        $text = $targetClosure()->getText();
        if ($text != $this->expectedText) {
            throw new UnexpectedValueException(
                sprintf("Element's text is different than expected. Found '%s' instead of '%s'", $text, $this->expectedText)
            );
        }
        return true;
    }
}

