<?php
namespace Athena\Configuration;

use Athena\Exception\InvalidSettingsPathString;

class Settings
{
    /**
     * @var array
     */
    private $settings;
    /**
     * @var boolean
     */
    private $reportEnabled;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns the setting with the given name.
     *
     * @param $name
     *
     * @return Alternatively
     *
     * @throws \Exception
     */
    public function get($name)
    {
        return new Alternatively($this->settings, $name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        return array_key_exists($name, $this->settings);
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->settings[$name] = $value;
    }

    /**
     * Returns the array containing the settings.
     *
     */
    public function getAll()
    {
        return $this->settings;
    }

    /**
     * Retrieve a setting through a path string.
     *
     * @param string $settingPathString Path string with the format: 'parentSetting.childSetting'
     *
     * @return \Athena\Configuration\Alternatively
     * @throws \Athena\Exception\InvalidSettingsPathString
     */
    public function getByPath($settingPathString)
    {
        $settingsPathKeys = explode('.', $settingPathString);

        // Our last setting, should be handled by the "Alternatively" class
        // This way the developer can decide on how he will handle the setting value presence or absence
        $settingPathLastKey = array_pop($settingsPathKeys);

        $currentSettings = $this->settings;
        foreach ($settingsPathKeys as $settingKey) {

            // if the path is not entirely found, let the developer handle it has he wishes, using the alternatively
            // class
            if (!array_key_exists($settingKey, $currentSettings)) {
                break;
            }

            $currentSettings = $currentSettings[$settingKey];
        }

        return new Alternatively($currentSettings, $settingPathLastKey);
    }
    
     /**
     * @return bool
     */
    public function isReportAvailable()
    {
        if ($this->exists('report') && $this->reportEnabled) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function disableReport(){
        $this->reportEnabled = false;
    }

    /**
     *
     */
    public function enableReport(){
        $this->reportEnabled = true;
    }
}

