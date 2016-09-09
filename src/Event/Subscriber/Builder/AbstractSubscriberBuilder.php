<?php
namespace Athena\Event\Subscriber\Builder;

use Athena\Configuration\Settings;
use Athena\Exception\DirectoryNotFoundException;
use Athena\Logger\RegexPurgeStrategy;
use SebastianBergmann\GlobalState\RuntimeException;

abstract class AbstractSubscriberBuilder
{
    /**
     * @var RegexPurgeStrategy
     */
    protected $withReportCleaning;
    /**
     * @var bool
     */
    protected $withScreenshots;
    /**
     * @var string
     */
    protected $outputPathName;
    /**
     * @var bool
     */
    protected $withTrafficLogger;

    /**
     * @param \Athena\Configuration\Settings   $settings
     *
     * @return BrowserSubscriberBuilder
     */
    public static function fromSettings(Settings $settings)
    {
        $outputDirectory   = $settings->getByPath('report.outputDirectory')->orDefaultTo($settings->get('athena_tests_directory')->orFail());
        $withReportClean   = $settings->getByPath('report.cleanOldReports')->orDefaultTo(true);
        $withTrafficLogger = $settings->getByPath('proxy.recording')->orDefaultTo(false);
        $withScreenshots = false;

        if ($settings->exists('browser')) {
            $withScreenshots = $settings->get('screenshots')->orDefaultTo(true);
        }

        $builder = new static($outputDirectory);

        if ($withTrafficLogger) {
            $builder->withTrafficLogger();
        }

        if ($withScreenshots) {
            $builder->withScreenshots();
        }

        if ($withReportClean) {
            $builder->withReportCleaning();
        }

        return $builder;
    }

    /**
     * BrowserSubscriberBuilder constructor.
     *
     * @param string $outputDirectory
     */
    public function __construct($outputDirectory)
    {
        $this->assertDirectoryExists($outputDirectory);

        $this->outputPathName = $outputDirectory;
    }

    /**
     * @return $this
     */
    public function withReportCleaning()
    {
        $this->withReportCleaning = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function withScreenshots()
    {
        $this->withScreenshots = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function withTrafficLogger()
    {
        $this->withTrafficLogger = true;
        return $this;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    abstract function build();

    /**
     * @param string $outputDirectory
     *
     * @throws \Athena\Exception\DirectoryNotFoundException
     */
    private function assertDirectoryExists($outputDirectory)
    {
        if (!file_exists($outputDirectory)) {
            if(!mkdir($outputDirectory)) {
                throw new RuntimeException(sprintf("Failed to create %d directory for Reports.", $outputDirectory));
            }
        }
    }
}

