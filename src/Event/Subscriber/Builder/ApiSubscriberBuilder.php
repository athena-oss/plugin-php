<?php
namespace Athena\Event\Subscriber\Builder;

use Athena\Athena;
use Athena\Event\Subscriber\ApiSubscriber;
use Athena\Logger\Interpreter\HtmlInterpreter;
use Athena\Logger\ProxyTrafficLogger;
use Athena\Stream\FileOutputStream;
use Athena\Stream\UniqueNameFileOutputStream;

class ApiSubscriberBuilder extends AbstractSubscriberBuilder
{
    /**
     * @return \Athena\Event\Subscriber\UnitSubscriber
     */
    public function build()
    {
        $interpreter  = new HtmlInterpreter('unit_report.twig');
        $outputStream = new FileOutputStream($this->outputPathName . "/report.html");

        $subscriber = new ApiSubscriber($interpreter, $outputStream);

        if ($this->withTrafficLogger) {
            $subscriber->setTrafficLogger(
                new ProxyTrafficLogger(Athena::proxy(), new UniqueNameFileOutputStream($this->outputPathName, 'har')));
        }

        return $subscriber;
    }
}

