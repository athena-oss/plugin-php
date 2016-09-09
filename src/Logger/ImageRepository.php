<?php
namespace Athena\Logger;

class ImageRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * ReportImageRepository constructor.
     *
     * @param string $outputDirectory
     */
    public function __construct($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * @param string $contents
     *
     * @return string File name where the image was stored in
     */
    public function write($contents)
    {
        $fileName = sprintf("%s/athenaimg_%s_%s.jpg", $this->outputDirectory, uniqid(), posix_getpid());

        file_put_contents($fileName, $contents);

        return $fileName;
    }
}

