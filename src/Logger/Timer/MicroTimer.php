<?php
namespace Athena\Logger\Timer;

use DomainException;

class MicroTimer implements TimerInterface
{
    /**
     * @var array
     */
    private $timers = [];

    /**
     * @inheritdoc
     */
    public function start($key)
    {
        $this->timers[$key] = microtime(true);
    }

    /**
     * @inheritdoc
     */
    public function stop($key)
    {
        if (!array_key_exists($key, $this->timers)) {
            throw new DomainException(sprintf("You cannot stop '%s' timer, as it was never initialized.", $key));
        }

        return microtime(true) - $this->timers[$key];
    }
}

