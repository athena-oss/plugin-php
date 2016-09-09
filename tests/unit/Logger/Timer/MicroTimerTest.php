<?php

namespace Athena\Tests\Logger\Timer;

use Athena\Logger\Timer\MicroTimer;
use PHPUnit_Framework_TestCase;

class MicroTimerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage You cannot stop 'ron' timer, as it was never initialized.
     */
    public function testStop_TimerWasNotStarted_ShouldThrowDomainException()
    {
        $timer = new MicroTimer();
        $timer->stop('ron');
    }
}