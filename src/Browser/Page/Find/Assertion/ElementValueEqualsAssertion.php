<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\UnexpectedValueException;

class ElementValueEqualsAssertion implements TargetDecoratorInterface
{
    private $expectedText;

    /**
     * TextEqualsAssertion constructor.
     * @param $compareToValue
     */
    public function __construct($compareToValue)
    {
        $this->expectedText = $compareToValue;
    }

    public function decorate($targetClosure, $locator)
    {
        $element = $targetClosure();

        if (is_array($element)) {
            throw new \Exception('Element should not be an array');
        }

        $text = strtolower($element->getTagName()) == "textarea" ? $element->getValue() : $element->getAttribute("value");
        if ($text != $this->expectedText) {
            throw new UnexpectedValueException(
                sprintf("Element's value is different than expected. Found '%s' instead of '%s'", $text, $this->expectedText)
            );
        }
        return true;
    }
}

