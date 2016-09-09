<?php
namespace Athena\Logger;

interface TrafficLoggerInterface extends WritableLoggerInterface
{
    /**
     * @return void
     */
    public function start();
}

