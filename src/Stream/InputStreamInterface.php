<?php
namespace Athena\Stream;

interface InputStreamInterface
{
    /**
     * @return boolean
     */
    public function valid();

    /**
     * @return string
     */
    public function read();

    /**
     * @return boolean
     */
    public function close();
}

