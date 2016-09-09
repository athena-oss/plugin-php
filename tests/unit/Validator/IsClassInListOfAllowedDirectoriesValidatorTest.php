<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 14/01/16
 * Time: 16:41
 */

namespace Athena\Tests\Validator;

use Athena\Validator\IsClassInListOfAllowedDirectoriesValidator;
use Phake;
use ReflectionClass;

class IsClassInListOfAllowedDirectoriesValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate_ClassExistsInValidDirectory_ShouldReturnTrue()
    {
        $fakeBaseDir = '/';
        $fakeFilterDirectories = ['codebase'];

        $fakeReflection = Phake::mock(ReflectionClass::class);

        Phake::when($fakeReflection)->getFileName()->thenReturn('/codebase/MyClass.php');

        $validatorObj = new IsClassInListOfAllowedDirectoriesValidator(
            $fakeBaseDir,
            $fakeFilterDirectories,
            $fakeReflection
        );

        $this->assertTrue($validatorObj->validate());
    }

    public function testValidate_ClassExistsInInvalidDirectory_ShouldReturnFalse()
    {
        $fakeBaseDir = '/';
        $fakeFilterDirectories = ['codebase'];

        $fakeReflection = Phake::mock(ReflectionClass::class);

        Phake::when($fakeReflection)->getFileName()->thenReturn('/invalidFolder/MyClass.php');

        $validatorObj = new IsClassInListOfAllowedDirectoriesValidator(
            $fakeBaseDir,
            $fakeFilterDirectories,
            $fakeReflection
        );

        $this->assertFalse($validatorObj->validate());
    }
}