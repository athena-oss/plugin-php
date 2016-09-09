<?php
namespace Athena\Logger;

class NoLogLogger implements LoggerInterface
{
    /**
     * @return void
     */
    public function log()
    {
        // void as intended
    }
}

