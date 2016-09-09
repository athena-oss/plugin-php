<?php
namespace Athena\Validator;

class IsInListOfAllowedDirectoriesValidator
{
    /**
     * @var string
     */
    private $baseDirectory;

    /**
     * @var array
     */
    private $allowedDirectories;

    /**
     * @var string
     */
    private $directoryToValidate;

    /**
     * IsInListOfAllowedDirectoriesValidator constructor.
     *
     * @param string $baseDirectory
     * @param array  $allowedDirectories
     * @param string $directoryToValidate
     */
    public function __construct($baseDirectory, array $allowedDirectories, $directoryToValidate)
    {
        $this->baseDirectory       = realpath($baseDirectory);
        $this->allowedDirectories  = $allowedDirectories;
        $this->directoryToValidate = $directoryToValidate;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (empty($this->allowedDirectories)) {
            return true;
        }

        foreach ($this->allowedDirectories as $relativeDir) {
            $filterDir = $this->getDirectory($relativeDir);
            if (stripos($this->directoryToValidate, $filterDir) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $relativeDirectory
     * @return string
     */
    private function getDirectory($relativeDirectory)
    {
        return $this->baseDirectory . $relativeDirectory;
    }
}

