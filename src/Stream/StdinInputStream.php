<?php
namespace Athena\Stream;

class StdinInputStream implements InputStreamInterface
{
    /**
     * @var resource
     */
    private $stdin;

    /**
     * StdinInputStream constructor.
     */
    public function __construct()
    {
        $this->stdin = fopen('php://stdin', 'r');
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return feof($this->stdin) !== true;
    }

    /**
     * @return string
     */
    public function read()
    {
        return fgets($this->stdin);
    }

    /**
     * @return boolean
     */
    public function close()
    {
        if ($this->stdin) {
            return fclose($this->stdin);
        }

        return true;
    }
}

