<?php
namespace Athena\Logger;

use Athena\Configuration\Settings;

class PurgeStrategyFactory
{
    /**
     * @param \Athena\Configuration\Settings $settings
     *
     * @return \Athena\Logger\NullPurgeStrategy|\Athena\Logger\RegexPurgeStrategy
     * @throws \Athena\Exception\SettingNotFoundException
     */
    public static function fromSettings(Settings $settings)
    {
        $targetDirectory = $settings->getByPath('report.outputDirectory')->orDefaultTo(null);
        $keepOldFiles    = $settings->getByPath('report.keepOldFiles')->orDefaultTo(false);

        if (!$keepOldFiles && !is_null($targetDirectory)) {
            return new RegexPurgeStrategy($targetDirectory);
        }

        return new NullPurgeStrategy();
    }
}

