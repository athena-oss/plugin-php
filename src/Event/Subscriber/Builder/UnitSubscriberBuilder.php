<?php
namespace Athena\Event\Subscriber\Builder;

use Athena\Event\Subscriber\UnitSubscriber;
use Athena\Logger\Interpreter\HtmlInterpreter;
use Athena\Stream\FileOutputStream;

class UnitSubscriberBuilder extends AbstractSubscriberBuilder
{
    /**
     * @return \Athena\Event\Subscriber\UnitSubscriber
     */
    public function build()
    {
        $interpreter  = new HtmlInterpreter('unit_report.twig');
        $outputStream = new FileOutputStream($this->outputPathName."/report.html");

        return new UnitSubscriber($interpreter, $outputStream);
    }
}

