<?php
namespace Athena\Tests\Holder;

use Athena\Holder\ClosureHolder;

class ValueHolder
{
    private $value;

    /**
     * ValueHolder constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}

class ClosureHolderTest extends \PHPUnit_Framework_TestCase
{

    public function testGet_ClosureWasNotInvoked_ShouldInvokeAndReturnValue()
    {
        $countHolder      = new ValueHolder(0);
        $wasInvokedHolder = new ValueHolder(false);
        $closure          = function () use ($countHolder, $wasInvokedHolder) {
            $wasInvokedHolder->setValue(true);
            $countHolder->setValue($countHolder->getValue()+1);
            return $countHolder->getValue();
        };

        $closureHolder = new ClosureHolder($closure);

        $this->assertEquals(1, $closureHolder->get());
        $this->assertTrue($wasInvokedHolder->getValue());
    }

    public function testGet_ClosureWasAlreadyInvoked_ShouldNotInvokeAndReturnSameValue()
    {
        $countHolder      = new ValueHolder(0);
        $wasInvokedHolder = new ValueHolder(false);
        $closure         = function () use ($countHolder, $wasInvokedHolder) {
            $wasInvokedHolder->setValue(true);
            $countHolder->setValue($countHolder->getValue()+1);
            return $countHolder->getValue();
        };

        $closureHolder = new ClosureHolder($closure);
        $closureHolder();
        $wasInvokedHolder->setValue(false);

        $this->assertEquals(1, $closureHolder->get());
        $this->assertFalse($wasInvokedHolder->getValue());
    }
}
