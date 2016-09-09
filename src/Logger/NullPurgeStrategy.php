<?php
namespace Athena\Logger;

class NullPurgeStrategy implements PurgeStrategyInterface
{
    /**
     * @return void
     */
    public function purge()
    {
        // void
    }
}

