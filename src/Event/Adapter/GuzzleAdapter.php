<?php
namespace Athena\Event\Adapter;

use Athena\Event\HttpTransactionCompleted;
use GuzzleHttp\Event\AbstractRetryableEvent;
use GuzzleHttp\Event\SubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GuzzleAdapter implements SubscriberInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    /**
     * BehatProxy constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function getEvents()
    {
        return [
            'complete' => ['onComplete'],
            'error'    => ['onComplete']
        ];
    }

    /**
     * @param \GuzzleHttp\Event\AbstractRetryableEvent $event
     */
    public function onComplete(AbstractRetryableEvent $event)
    {
        $this->eventDispatcher->dispatch(HttpTransactionCompleted::AFTER,
            new HttpTransactionCompleted(
                $event->getTransaction()->request,
                $event->getTransaction()->response,
                $event->getRequest()->getUrl(),
                $event->getRequest()->getMethod()
            )
        );
    }
}

