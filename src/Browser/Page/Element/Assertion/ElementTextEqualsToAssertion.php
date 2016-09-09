<?php
namespace Athena\Browser\Page\Element\Assertion;

use Athena\Browser\Page\Element\Find\ElementFinderInterface;
use Closure;
use UnexpectedValueException;

class ElementTextEqualsToAssertion extends AbstractElementAssertion
{
    /**
     * @var string
     */
    private $expectedString;

    /**
     * ElementValueEqualsToAssertion constructor.
     *
     * @param string                                                   $expectedString
     * @param \Athena\Browser\Page\Element\Find\ElementFinderInterface $elementFinder
     */
    public function __construct($expectedString, ElementFinderInterface $elementFinder)
    {
        $this->expectedString = $expectedString;

        parent::__construct($elementFinder);
    }

    /**
     * @param \Closure $getElementClosure
     *
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function assert(Closure $getElementClosure)
    {
        $element = $getElementClosure();
        $elementText = $element->getText();

        if ($this->expectedString != $elementText) {
            throw new UnexpectedValueException(
                sprintf(
                    "Element's innerHTML is different than expected. Found '%s' instead of '%s'",
                    $elementText,
                    $this->expectedString
                )
            );
        }
    }
}

