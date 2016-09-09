<?php
namespace Athena\Holder;

class ClosureHolder
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * @var mixed
     */
    private $instance;

    /**
     * @var bool
     */
    private $hasBeenInvoked;

    /**
     * Holder constructor.
     * @param $closure
     */
    public function __construct(callable $closure)
    {
        $this->closure        = $closure;
        $this->instance       = null;
        $this->hasBeenInvoked = false;
    }

    /**
     * The __invoke method is called when a script tries to call an object as a function.
     *
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.invoke
     */
    public function __invoke()
    {
        return $this->get();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        if (!$this->hasBeenInvoked) {
            $closure              = $this->closure;
            $this->instance       = $closure();
            $this->hasBeenInvoked = true;
        }
        return $this->instance;
    }
}

