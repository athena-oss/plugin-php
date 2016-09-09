<?php
namespace Athena\Configuration;

use Athena\Exception\FileNotFoundException;
use Athena\Exception\FileNotReadableException;
use Athena\Exception\InvalidJsonStringException;
use InvalidArgumentException;

class SettingsFactory
{
    /**
     * @param array $settings
     *
     * @return \Athena\Configuration\Settings
     */
    public static function fromArray(array $settings)
    {
        return new Settings($settings);
    }

    /**
     * @param string $fileName
     *
     * @return \Athena\Configuration\Settings
     * @throws \InvalidArgumentException
     * @throws \Athena\Exception\FileNotFoundException
     * @throws \Athena\Exception\FileNotReadableException
     * @throws \Athena\Exception\InvalidJsonStringException
     */
    public static function fromJsonFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException(sprintf("%s, was not found. Current working dir: %s.", $fileName, getcwd()));
        }

        if (!is_readable($fileName)) {
            throw new FileNotReadableException(sprintf("%s, is not readable.", $fileName));
        }

        $jsonArray = json_decode(file_get_contents($fileName), true);

        if ($jsonArray === null) {
            throw new InvalidJsonStringException(json_last_error_msg());
        }

        return new Settings($jsonArray);
    }
}

