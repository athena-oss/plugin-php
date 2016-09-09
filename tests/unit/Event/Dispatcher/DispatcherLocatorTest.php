<?php

namespace Athena\Tests\Event\Dispatcher;

use Athena\Athena;
use Athena\Event\Dispatcher\DispatcherLocator;

class DispatcherLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testLocate_AthenaEventDispatcherIsInstantiatedPreviously_ShouldReturnSameEventDispatcherInstance()
    {
        if (!defined('ATHENA_TESTS_TYPE')) {
            define('ATHENA_TESTS_TYPE', 'unit');
        }

        if (!defined('ATHENA_TESTS_DIRECTORY')) {
            define('ATHENA_TESTS_DIRECTORY', getcwd());
        }

        $actualEventDispatcher   = Athena::getInstance()->getEventDispatcher();
        $actualDispatcherLocator = new DispatcherLocator();

        $this->assertSame($actualEventDispatcher, $actualDispatcherLocator->locate());
    }
}
