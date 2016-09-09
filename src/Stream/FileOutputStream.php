<?php
namespace Athena\Stream;

class FileOutputStream implements OutputStreamInterface
{

    private $fileName;

    /**
     * FileOutputStream constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $contents
     * @return void
     */
    public function write($contents)
    {
        file_put_contents($this->fileName, $contents);
    }

    /**
     * @return boolean
     */
    public function close()
    {
        return false;
    }
}

