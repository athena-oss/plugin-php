<?php
namespace Athena\Validator;

use Athena\Athena;
use ReflectionClass;

trait TestAllowedValidator
{
    /**
     * @param string $type
     * @return bool
     */
    public function validateTestType($type)
    {
        $validator = new IsClassInListOfAllowedDirectoriesValidator(
            Athena::getInstance()->getTestsDirectory(),
            Athena::settings()->get('filter_directories')->orDefaultTo([]),
            new ReflectionClass($this)
        );

        if (!$validator->validate()) {
            return false;
        }

        return Athena::getInstance()->getTestsType() == $type;
    }
}

