<?php
namespace Athena\Logger;

use DirectoryIterator;
use UnexpectedValueException;

class RegexPurgeStrategy implements PurgeStrategyInterface
{
    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * ReportCleaner constructor.
     *
     * @param string $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Clean old report files.
     */
    public function purge()
    {
        // in case the directory was already deleted, there's no point on purging it
        if (!file_exists($this->targetDirectory)) {
            return;
        }

        $dirIterator = new DirectoryIterator($this->targetDirectory);

        foreach ($dirIterator as $dirItem) {
            if ($dirItem->isFile()) {
                if (preg_match("/(^athenaimg_|report\\.json|report\\.html|_athena\\.har)/i", $dirItem->getFilename())) {
                    unlink($dirItem->getPathname());
                }
            }
        }
    }
}

