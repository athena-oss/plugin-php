<?php
namespace Athena\Browser\Page\Find\Assertion;

use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\EmptyResultException;
use Athena\Exception\NotAllElementsApplyToCriteriaException;
use Athena\Exception\NotAnArrayException;
use Facebook\WebDriver\Remote\RemoteWebElement;

class AllElementsApplyToAssertion implements TargetDecoratorInterface
{
    /**
     * @var callable
     */
    private $criteria;

    /**
     * @var string
     */
    private $criteriaDescription;

    /**
     * AllElementsApplyToAssertion constructor.
     * @param callable $criteria
     * @param string $criteriaDescription
     */
    public function __construct(callable $criteria, $criteriaDescription = "<user function>")
    {
        $this->criteria            = $criteria;
        $this->criteriaDescription = $criteriaDescription;
    }


    public function decorate($targetClosure, $locator)
    {
        $elements     = $targetClosure();
        $nrOfElements = sizeof($elements);

        if ($nrOfElements === 0) {
            throw new EmptyResultException('No elements found.');
        }

        if (!is_array($elements)) {
            throw new NotAnArrayException('Elements is not an array.');
        }

        $criteria = $this->criteria;
        $nrOfElementsThatApply = array_reduce($elements, function ($carry, RemoteWebElement $currentElement) use ($criteria) {
            return $criteria($currentElement) === true ? $carry + 1 : $carry;
        });

        if ($nrOfElements !== $nrOfElementsThatApply) {
            throw new NotAllElementsApplyToCriteriaException(
                sprintf(
                    "Number of elements that are '%s' [%d] is different that total of elements [%d]",
                    $this->criteriaDescription,
                    $nrOfElementsThatApply,
                    $nrOfElements
                )
            );
        }

        return true;
    }
}

