<?php
namespace Athena\Tests\Validator;

use Athena\Validator\IsInListOfAllowedDirectoriesValidator;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class IsInListOfAllowedDirectoriesValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidate_NoDirectoriesWereProvided_ShouldReturnTrue()
    {
        $validator = new IsInListOfAllowedDirectoriesValidator('', [], 'dummy');
        $this->assertTrue($validator->validate());
    }

    public function testValidate_DirectoriesWereProvidedAndGivenDirectoryInsideOfAuthorizedList_ShouldReturnTrue()
    {
        $fakeDir = vfsStream::setup();
        $authorizedDirectories = ['/dir1', '/dir2', '/dir3'];
        $validator = new IsInListOfAllowedDirectoriesValidator($fakeDir->url(), $authorizedDirectories, $fakeDir->url().'/dir1/innerfolder');
        $this->assertTrue($validator->validate());
    }

    public function testValidate_DirectoriesWereProvidedAndGivenDirectoryIsNotInsideOfAuthorizedList_ShouldReturnFalse()
    {
        $authorizedDirectories = ['/dir1', '/dir2', '/dir3'];
        $validator = new IsInListOfAllowedDirectoriesValidator('/tmp', $authorizedDirectories, '/tmp/dir4/innerfolder');
        $this->assertFalse($validator->validate());
    }
}
