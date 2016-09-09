<?php
namespace Athena\Logger\Timer;

interface TimerInterface
{
    /**
     * @param string $key
     */
    public function start($key);

    /**
     * @param string $key
     *
     * @return float Returns total execution time.
     */
    public function stop($key);
}

