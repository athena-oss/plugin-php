<?php
namespace Athena\Stream;

class UniqueNameFileOutputStream implements OutputStreamInterface
{
    /**
     * @var string
     */
    private $fileExtension;
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * UniqueNameFileOutputStream constructor.
     *
     * @param string $outputDirectory
     * @param string $fileExtension
     */
    public function __construct($outputDirectory, $fileExtension)
    {
        $this->fileExtension = $fileExtension;
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * @param string $contents
     *
     * @return int
     */
    public function write($contents)
    {
        $fileName = $this->outputDirectory.DIRECTORY_SEPARATOR.uniqid(posix_getpid()).'.'.$this->fileExtension;
        file_put_contents($fileName, $contents);
        return $fileName;
    }

    /**
     * @return boolean
     */
    public function close()
    {
        // void
    }
}

