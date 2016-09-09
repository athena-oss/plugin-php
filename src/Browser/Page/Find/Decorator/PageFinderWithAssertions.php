<?php
namespace Athena\Browser\Page\Find\Decorator;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\Assertion\AllElementsApplyToAssertion;
use Athena\Browser\Page\Find\Assertion\AllElementsAreHiddenAssertion;
use Athena\Browser\Page\Find\Assertion\AllElementsAreVisibleAssertion;
use Athena\Browser\Page\Find\Assertion\AtLeastOneElementAppliesToAssertion;
use Athena\Browser\Page\Find\Assertion\ElementExistsAtLeastOnceAssertion;
use Athena\Browser\Page\Find\Assertion\ElementExistsOnlyOnceAssertion;
use Athena\Browser\Page\Find\Assertion\ElementShouldNotExistAssertion;
use Athena\Browser\Page\Find\Assertion\ElementTextEqualsAssertion;
use Athena\Browser\Page\Find\Assertion\ElementValueEqualsAssertion;

class PageFinderWithAssertions extends AbstractPageFinderDecorator
{
    /**
     * @return $this
     */
    public function existsOnlyOnce()
    {
        $this->registerDecorator(new ElementExistsOnlyOnceAssertion());
        return $this;
    }

    /**
     * @return $this
     */
    public function existsAtLeastOnce()
    {
        $this->registerDecorator(new ElementExistsAtLeastOnceAssertion());
        return $this;
    }

    /**
     * @return $this
     */
    public function doesNotExist()
    {
        $this->registerDecorator(new ElementShouldNotExistAssertion());
        return $this;
    }

    /**
     * @param $expectedText
     * @return $this
     */
    public function textEquals($expectedText)
    {
        $this->registerDecorator(new ElementTextEqualsAssertion($expectedText));
        return $this;
    }

    /**
     * @param $expectedValue
     * @return $this
     */
    public function valueEquals($expectedValue)
    {
        $this->registerDecorator(new ElementValueEqualsAssertion($expectedValue));
        return $this;
    }

    /**
     * @param callable $criteria
     * @param string $criteriaDescription
     * @return $this
     */
    public function allApplyTo(callable $criteria, $criteriaDescription = '<user function>')
    {
        $this->registerDecorator(new AllElementsApplyToAssertion($criteria, $criteriaDescription));
        return $this;
    }

    /**
     * @param callable $criteria
     * @param string $criteriaDescription
     * @return $this
     */
    public function anyAppliesTo(callable $criteria, $criteriaDescription = '<user function>')
    {
        $this->registerDecorator(new AtLeastOneElementAppliesToAssertion($criteria, $criteriaDescription));
        return $this;
    }

    /**
     * @return $this
     */
    public function allAreVisible()
    {
        $this->registerDecorator(new AllElementsAreVisibleAssertion());
        return $this;
    }

    /**
     * @return $this
     */
    public function allAreHidden()
    {
        $this->registerDecorator(new AllElementsAreHiddenAssertion());
        return $this;
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->getPageFinder()->getBrowser();
    }
}

