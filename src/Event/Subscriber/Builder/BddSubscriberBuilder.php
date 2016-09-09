<?php
namespace Athena\Event\Subscriber\Builder;

use Athena\Athena;
use Athena\Event\Subscriber\BddSubscriber;
use Athena\Logger\ImageRepository;
use Athena\Logger\Interpreter\DelimitedJsonInterpreter;
use Athena\Logger\ProxyTrafficLogger;
use Athena\Logger\Timer\MicroTimer;
use Athena\Stream\NamedPipeOutputStream;
use Athena\Stream\UniqueNameFileOutputStream;

class BddSubscriberBuilder extends AbstractSubscriberBuilder
{
    /**
     * @inheritdoc
     */
    public function build()
    {
        $interpreter  = new DelimitedJsonInterpreter(DelimitedJsonInterpreter::NEW_LINE);
        $outputStream = new NamedPipeOutputStream(ATHENA_REPORT_PIPE_NAME);
        $timer        = new MicroTimer();
        $subscriber   = new BddSubscriber($interpreter, $outputStream, $timer);

        if ($this->withTrafficLogger) {
            $subscriber->setTrafficLogger(
                new ProxyTrafficLogger(Athena::proxy(), new UniqueNameFileOutputStream($this->outputPathName, 'har')));
        }

        if ($this->withScreenshots) {
            $subscriber->setImageRepository(new ImageRepository($this->outputPathName));
        }

        return $subscriber;
    }
}

