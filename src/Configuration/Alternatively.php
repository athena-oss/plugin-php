<?php
namespace Athena\Configuration;

use Athena\Exception\SettingNotFoundException;

class Alternatively
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var string
     */
    private $name;

    /**
     * Alternatively constructor.
     * @param array $settings
     * @param string $name
     */
    public function __construct(array $settings, $name)
    {
        $this->settings = $settings;
        $this->name = $name;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function orDefaultTo($value)
    {
        $directValue = array_key_exists($this->name, $this->settings) ? $this->settings[$this->name] : $value;
        return $this->getFromEnvironmentVariableIfExists($directValue);
    }

    /**
     * @return mixed
     * @throws SettingNotFoundException
     */
    public function orFail()
    {
        if (!array_key_exists($this->name, $this->settings)) {
            throw new SettingNotFoundException("Setting does not exist for key '$this->name'");
        }

        return $this->getFromEnvironmentVariableIfExists($this->settings[$this->name]);
    }

    /**
     * @param $originalValue
     * @return mixed
     * @throws SettingNotFoundException
     */
    public function getFromEnvironmentVariableIfExists($originalValue)
    {
        if (is_array($originalValue)) {
            foreach ($originalValue as $k => $value) {
                $originalValue[$k] = $this->getFromEnvironmentVariableIfExists($value);
            }
            return $originalValue;
        }

        $matches = [];
        $found   = preg_match('/ENV\[([^|]+?)(\|(.+))?\]/u', $originalValue, $matches);
        if ($found === 1) {
            $default = isset($matches[3]) ? $matches[3] : false;
            $value   = getenv($matches[1]) ? : $default;
            if ($value === false) {
                throw new SettingNotFoundException(sprintf("Environment variable '%s' was not found", $matches[1]));
            }
            return $value;
        }
        return $originalValue;
    }
}
