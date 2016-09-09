<?php
namespace Athena\Browser\Page\Element\Find;

use Athena\Browser\Page\Element\Assertion\ElementDoesNotExistAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsDisplayedAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsEnabledAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsHiddenAssertion;
use Athena\Browser\Page\Element\Assertion\ElementIsSelectedAssertion;
use Athena\Browser\Page\Element\Assertion\ElementTextEqualsToAssertion;
use Athena\Browser\Page\Element\Assertion\ElementValueEqualsToAssertion;
use Athena\Browser\Page\Element\ElementActionInterface;

class ElementFindWithAssertions
{
    /**
     * @var \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    private $elementFinder;

    /**
     * ElementFindAndAssert constructor.
     *
     * @param \Athena\Browser\Page\Element\Find\ElementFinderInterface $elementFinder
     */
    public function __construct(ElementFinderInterface $elementFinder)
    {
        $this->elementFinder = $elementFinder;
    }

    /**
     * @return $this
     * @throws \Athena\Exception\ElementNotExpectedException
     * @throws \Athena\Exception\StopChainException
     */
    public function doesNotExist()
    {
        $this->elementFinder = new ElementDoesNotExistAssertion($this->elementFinder);
        return $this;
    }

    /**
     * @return $this
     * @throws \AssertionError
     */
    public function isDisplayed()
    {
        $this->elementFinder = new ElementIsDisplayedAssertion($this->elementFinder);
        return $this;
    }

    /**
     * @return $this
     * @throws \AssertionError
     */
    public function isHidden()
    {
        $this->elementFinder = new ElementIsHiddenAssertion($this->elementFinder);
        return $this;
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFindWithAssertions
     */
    public function isEnabled()
    {
        $this->elementFinder = new ElementIsEnabledAssertion($this->elementFinder);
        return $this;
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFindWithAssertions
     */
    public function isSelected()
    {
        $this->elementFinder = new ElementIsSelectedAssertion($this->elementFinder);
        return $this;
    }

    /**
     * @param string $expectedValue
     *
     * @return $this
     * @throws \Athena\Exception\UnexpectedValueException
     * @throws \Exception
     */
    public function valueEqualTo($expectedValue)
    {
        $this->elementFinder = new ElementValueEqualsToAssertion($expectedValue, $this->elementFinder);
        return $this;
    }

    /**
     * @param string $expectedValue
     *
     * @return $this
     * @throws \Athena\Exception\UnexpectedValueException
     */
    public function textEqualTo($expectedValue)
    {
        $this->elementFinder = new ElementTextEqualsToAssertion($expectedValue, $this->elementFinder);
        return $this;
    }

    /**
     * @return \Athena\Browser\Page\Element\Find\ElementFinderInterface
     */
    public function thenFind()
    {
        return $this->elementFinder;
    }
}

