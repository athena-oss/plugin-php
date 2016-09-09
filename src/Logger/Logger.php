<?php
namespace Athena\Logger;

use Athena\Logger\Interpreter\InterpreterInterface;
use Athena\Stream\InputStreamInterface;
use Athena\Stream\OutputStreamInterface;

class Logger implements LoggerInterface
{
    /**
     * @var \Athena\Stream\InputStreamInterface
     */
    private $inputStream;
    /**
     * @var \Athena\Logger\Interpreter\InterpreterInterface
     */
    private $interpreter;
    /**
     * @var \Athena\Stream\OutputStreamInterface
     */
    private $outputStream;

    /**
     * Logger constructor.
     *
     * @param \Athena\Stream\InputStreamInterface             $inputStream
     * @param \Athena\Logger\Interpreter\InterpreterInterface $interpreter
     * @param \Athena\Stream\OutputStreamInterface            $outputStream
     */
    public function __construct(
        InputStreamInterface $inputStream,
        InterpreterInterface $interpreter,
        OutputStreamInterface $outputStream
    ) {
        $this->inputStream = $inputStream;
        $this->interpreter = $interpreter;
        $this->outputStream = $outputStream;
    }

    public function log()
    {
        while ($this->inputStream->valid()) {
            $contents = $this->inputStream->read();
            $this->outputStream->write($this->interpreter->interpret($contents));
        }
    }
}

