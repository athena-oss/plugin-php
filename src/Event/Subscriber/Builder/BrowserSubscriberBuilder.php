<?php
namespace Athena\Event\Subscriber\Builder;

use Athena\Athena;
use Athena\Event\Subscriber\BrowserSubscriber;
use Athena\Logger\ImageRepository;
use Athena\Logger\Interpreter\HtmlInterpreter;
use Athena\Logger\ProxyTrafficLogger;
use Athena\Stream\FileOutputStream;
use Athena\Stream\UniqueNameFileOutputStream;

class BrowserSubscriberBuilder extends AbstractSubscriberBuilder
{
    /**
     * @return \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    public function build()
    {
        $interpreter  = new HtmlInterpreter('browser_report.twig');
        $outputStream = new FileOutputStream($this->outputPathName."/report.html");

        $subscriber = new BrowserSubscriber($interpreter, $outputStream);

        if ($this->withTrafficLogger) {
            $subscriber->setTrafficLogger(
                new ProxyTrafficLogger(Athena::proxy(), new UniqueNameFileOutputStream($this->outputPathName, 'har')));
        }

        if ($this->withScreenshots) {
            $subscriber->setScreenshotRepository(new ImageRepository($this->outputPathName));
        }

        return $subscriber;
    }
}

