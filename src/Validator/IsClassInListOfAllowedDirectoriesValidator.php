<?php
namespace Athena\Validator;

use ReflectionClass;

class IsClassInListOfAllowedDirectoriesValidator extends IsInListOfAllowedDirectoriesValidator
{
    /**
     * Constructs a test case with the given name.
     *
     * @param string           $baseDirectory
     * @param array            $allowedDirectories
     * @param \ReflectionClass $classReflection
     */
    public function __construct($baseDirectory, array $allowedDirectories, ReflectionClass $classReflection)
    {
        $directoryToValidate = dirname($classReflection->getFileName());

        parent::__construct($baseDirectory, $allowedDirectories, $directoryToValidate);
    }
}

