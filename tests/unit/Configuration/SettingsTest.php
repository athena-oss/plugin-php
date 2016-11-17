<?php
namespace Athena\Tests\Configuration;

use Athena\Configuration\Alternatively;
use Athena\Configuration\Settings;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    public function testSet_RandomValueIsSet_ShouldSetProperty()
    {
        $settingsObj = new Settings([]);
        $settingsObj->set('ron', 'weasly');

        $this->assertArraySubset($settingsObj->getAll(), ['ron' => 'weasly']);
    }

    /**
     * @test
     */
    public function testGet_MethodIsInvoked_ShouldReturnAlternativelyInstance()
    {
        $this->assertInstanceOf(Alternatively::class, (new Settings([]))->get('xpto'));
    }

    /**
     * @test
     */
    public function testOrDefaultTo_ValueExistsInRepository_ShouldReturnStoredValue()
    {
        $settings = new Settings(['key' => 1234]);
        $this->assertEquals(1234, $settings->get('key')->orDefaultTo(4321));
    }

    /**
     * @test
     */
    public function testOrDefaultTo_ValueDoesNotExistInRepository_ShouldReturnDefaultValue()
    {
        $settings = new Settings([]);
        $this->assertEquals(4321, $settings->get('key')->orDefaultTo(4321));
    }

    /**
     * @test
     * @expectedException \Athena\Exception\SettingNotFoundException
     */
    public function testOrFail_ValueDoesNotExistInRepository_ShouldThrowSettingNotFoundException()
    {
        $settings = new Settings([]);
        $settings->get('key')->orFail();
    }

    /**
     * @test
     */
    public function testOrFail_ValueDoesNotExistInRepository_ShouldReturnStoredValue()
    {
        $settings = new Settings(['key' => 1234]);
        $this->assertEquals(1234, $settings->get('key')->orFail());
    }

    /**
     * @test
     */
    public function testGetSettings_SettingsIsGivenArray_ShouldReturnGivenArray()
    {
        $settings = new Settings(['key' => 1234]);
        $this->assertEquals(['key' => 1234], $settings->getAll());
    }

    /**
     * @test
     */
    public function testGet_SettingExistsAsEnvironmentVariable_ShouldReturnValueFromEnvironmentVariable()
    {
        $settings = new Settings(['key' => 'ENV[spinpans]']);
        putenv('spinpans=joe');
        $this->assertEquals('joe', $settings->get('key')->orFail());
    }

    /**
     * @test
     * @expectedException \Athena\Exception\SettingNotFoundException
     */
    public function testGet_SettingExistsAsEnvironmentVariable_ShouldThrowSettingNotFoundException()
    {
        $settings = new Settings(['key' => 'ENV[spinpans]']);
        // unsetting the environment variable
        putenv('spinpans');
        $settings->get('key')->orFail();
    }

    /**
     * @test
     */
    public function testGet_SettingExistsAsEnvironmentVariable_ShouldNotReturnDefaultValueFromEnvironmentVariable()
    {
        $settings = new Settings(['key' => 'ENV[spinpans|james]']);
        putenv('spinpans=joe');
        $this->assertEquals('joe', $settings->get('key')->orFail());
    }

    /**
     * @test
     */
    public function testGet_SettingDoesNotExistAsEnvironmentVariable_ShouldReturnDefaultValueFromEnvironmentVariable()
    {
        $settings = new Settings(['key' => 'ENV[spinpans|james]']);
        // unset environment variable
        putenv('spinpans');
        $this->assertEquals('james', $settings->get('key')->orFail());
    }

    /**
     * @test
     */
    public function testGet_SettingIsArrayAndValueExistsAsEnvironmentVariable_ShouldReturnValueFromEnvironmentVariable()
    {
        $settings = new Settings(['key' => ['name' => 'ENV[spinpans]']]);
        putenv('spinpans=joe');
        $array = $settings->get('key')->orFail();
        $this->assertEquals('joe', $array['name']);
    }

    public function testGetByPath_SettingIsArrayWithTwoLevelsAndPathStringIsValid_ShouldReturnAlternativelyInstance()
    {
        $settings = new Settings(['level1' => ['level2' => 'ron']]);

        $this->assertInstanceOf(Alternatively::class, $settings->getByPath('level1.level2'));
    }

    public function testGetByPath_SettingIsArrayWithTwoLevelsAndParentLevelDoesNotExist_ShouldReturnAlternativelyInstance()
    {
        $settings = new Settings(['level1' => ['level2' => 'ron']]);

        $this->assertInstanceOf(Alternatively::class,  $settings->getByPath('level5.level2'));
    }

    public function testGetByPath_SettingIsArrayWithOneLevelAndPathStringIsValid_ShouldReturnAlternativelyInstance()
    {
        $settings = new Settings(['level1' => 'ron']);

        $this->assertInstanceOf(Alternatively::class, $settings->getByPath('level1'));
    }
}
