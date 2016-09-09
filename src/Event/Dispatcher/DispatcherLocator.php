<?php
namespace Athena\Event\Dispatcher;

use Athena\Athena;

class DispatcherLocator
{
    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function locate()
    {
        return Athena::getInstance()->getEventDispatcher();
    }
}

