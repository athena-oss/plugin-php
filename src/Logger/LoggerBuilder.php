<?php
namespace Athena\Logger;

use Athena\Logger\Interpreter\InterpreterInterface;
use Athena\Stream\InputStreamInterface;
use Athena\Stream\OutputStreamInterface;

class LoggerBuilder
{
    /**
     * @var InputStreamInterface
     */
    private $inputStream;

    /**
     * @var InterpreterInterface
     */
    private $interpreter;

    /**
     * @var OutputStreamInterface
     */
    private $outputStream;

    /**
     * @param \Athena\Stream\InputStreamInterface $inputStream
     *
     * @return LoggerBuilder
     */
    public function readWith(InputStreamInterface $inputStream)
    {
        $this->inputStream = $inputStream;
        return $this;
    }

    /**
     * @param \Athena\Logger\Interpreter\InterpreterInterface $interpreter
     *
     * @return LoggerBuilder
     */
    public function parseWith(InterpreterInterface $interpreter)
    {
        $this->interpreter = $interpreter;
        return $this;
    }

    /**
     * @param \Athena\Stream\OutputStreamInterface $outputStream
     *
     * @return LoggerBuilder
     */
    public function printWith(OutputStreamInterface $outputStream)
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function build()
    {
        return new Logger(
            $this->inputStream,
            $this->interpreter,
            $this->outputStream
        );
    }
}

