<?php
namespace Athena\Stream;

class NamedPipeOutputStream implements OutputStreamInterface
{
    private $pathName;
    private $pipeHandle;

    /**
     * NamedPipeOutputStream constructor.
     * @param $pathName
     */
    public function __construct($pathName)
    {
        $this->pathName   = $pathName;
        $this->pipeHandle = fopen($this->pathName, 'a');
    }

    /**
     * @param string $contents
     * @return int
     */
    public function write($contents)
    {
        return fwrite($this->pipeHandle, $contents);
    }

    /**
     * @return boolean
     */
    public function close()
    {
        if ($this->pipeHandle) {
            return fclose($this->pipeHandle);
        }
        return false;
    }
}

