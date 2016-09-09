<?php
namespace Athena\Logger;

interface PurgeStrategyInterface
{
    /**
     * @return void
     */
    public function purge();
}

