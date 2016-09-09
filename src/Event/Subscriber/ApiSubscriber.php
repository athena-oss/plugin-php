<?php
namespace Athena\Event\Subscriber;

use Athena\Event\HttpTransactionCompleted;

class ApiSubscriber extends UnitSubscriber
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        $events = parent::getSubscribedEvents();

        $events[HttpTransactionCompleted::AFTER] = 'afterComplete';

        return $events;
    }

    /**
     * @param \Athena\Event\HttpTransactionCompleted $event
     */
    public function afterComplete(HttpTransactionCompleted $event)
    {
        $this->report->addHttpTransaction($event->getRequest(), $event->getResponse());
    }
}

