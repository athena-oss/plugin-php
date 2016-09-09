<?php
namespace Athena\Browser\Page\Find\Wait;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\Decorator\TargetDecoratorInterface;
use Athena\Exception\CriteriaNotMetException;

abstract class AbstractWait implements TargetDecoratorInterface
{
    /**
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * @var int
     */
    protected $timeOutInSeconds;

    /**
     * AbstractWait constructor.
     *
     * @param BrowserInterface $browser
     * @param int $timeOutInSeconds
     */
    public function __construct(BrowserInterface $browser, $timeOutInSeconds)
    {
        $this->browser  = $browser;
        $this->timeOutInSeconds = $timeOutInSeconds;
    }

    /**
     * @param $targetClosure
     * @param $locator
     *
     * @return bool
     * @throws CriteriaNotMetException
     */
    public function decorate($targetClosure, $locator)
    {
        try {
            $validator = function () use ($targetClosure, $locator) {
                return $this->validate($targetClosure, $locator);
            };

            $this->browser->wait($this->timeOutInSeconds, 50)->until($validator);
            return true;
        } catch (\Exception $e) {
            throw new CriteriaNotMetException($e->getMessage());
        }
    }

    /**
     * @param $targetClosure
     * @param null $locator
     * @return mixed
     */
    abstract protected function validate($targetClosure, $locator = null);
}

