<?php
namespace Athena\Stream;

interface OutputStreamInterface
{
    /**
     * @param string $contents
     * @return int
     */
    public function write($contents);

    /**
     * @return boolean
     */
    public function close();
}

