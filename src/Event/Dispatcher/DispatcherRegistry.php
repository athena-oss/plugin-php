<?php
namespace Athena\Event\Dispatcher;

use Athena\Configuration\Settings;
use Athena\Event\Subscriber\Builder\ApiSubscriberBuilder;
use Athena\Event\Subscriber\Builder\BddSubscriberBuilder;
use Athena\Event\Subscriber\Builder\BrowserSubscriberBuilder;
use Athena\Event\Subscriber\Builder\UnitSubscriberBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DispatcherRegistry
{
    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Athena\Configuration\Settings                              $settings
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     * @throws \Athena\Exception\SettingNotFoundException
     */
    public static function registerSubscriberFromSettings(EventDispatcherInterface $eventDispatcher, Settings $settings)
    {
        if (!$settings->exists('report')) {
            return;
        }

        $builder  = null;
        $testType = $settings->get('athena_tests_type')->orFail();

        switch ($testType) {
            case 'browser':
                $builder = BrowserSubscriberBuilder::fromSettings($settings);
                break;

            case 'unit':
                $builder = UnitSubscriberBuilder::fromSettings($settings);
                break;

            case 'bdd':
                $builder = BddSubscriberBuilder::fromSettings($settings);
                break;

            case 'api':
                $builder = ApiSubscriberBuilder::fromSettings($settings);
                break;

            default:
                return;
        }

        $eventDispatcher->addSubscriber($builder->build());
    }
}

