<?php
namespace Athena\Tests\Configuration;

use Athena\Configuration\SettingsFactory;
use org\bovigo\vfs\vfsStream;
use Athena\Configuration\Settings;

class SettingsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $fakeRootDir;

    public function setUp()
    {
        $this->fakeRootDir = vfsStream::setup();
    }

    public function testFromArray_GivenEmptyArray_ShouldReturnEmptySettingsInstance()
    {
        $emptySettings = SettingsFactory::fromArray([]);

        $this->assertEquals(new Settings([]), $emptySettings);
        $this->assertEmpty($emptySettings->getAll());
    }

    public function testFromArray_GivenFilledInArray_ShouldReturnSettingsInstance()
    {
        $settingsArray    = ['selenium' => 'wizardry'];
        $settingsInstance = SettingsFactory::fromArray(['selenium' => 'wizardry']);

        $this->assertEquals(new Settings($settingsArray), $settingsInstance);
        $this->assertArraySubset($settingsArray, $settingsInstance->getAll());
    }

    /**
     * @expectedException \Athena\Exception\FileNotFoundException
     */
    public function testFromJsonFile_FileDoesNotExist_ShouldThrowFileNotFoundException()
    {
        SettingsFactory::fromJsonFile(vfsStream::url('thisFileDoesNotExist.json'));
    }

    /**
     * @expectedException \Athena\Exception\FileNotReadableException
     */
    public function testFromJsonFile_FileExistsButIsNotReadable_ShouldThrowFileNotReadableException()
    {
        $mockedFile = vfsStream::newFile('NotReadable.json', 024);
        $mockedFile->at($this->fakeRootDir);

        SettingsFactory::fromJsonFile($mockedFile->url());
    }

    /**
     * @expectedException \Athena\Exception\InvalidJsonStringException
     */
    public function testFromJsonFile_FileExistsButIsNotValidJson_ShouldThrowInvalidJsonStringException()
    {
        $mockedFile = vfsStream::newFile('ContainsInvalid.json');
        $mockedFile->setContent('{ jsonProperty: "value"');
        $mockedFile->at($this->fakeRootDir);

        SettingsFactory::fromJsonFile($mockedFile->url());
    }

    public function testFromJsonFile_FileExistsAndContainsValidJson_ShouldReturnSettingsObject()
    {
        $mockedFile = vfsStream::newFile('ContainsValid.json');
        $mockedFile->setContent('{"key" : "value"}');
        $mockedFile->at($this->fakeRootDir);

        $this->assertInstanceOf(Settings::class, SettingsFactory::fromJsonFile($mockedFile->url()));
    }
}