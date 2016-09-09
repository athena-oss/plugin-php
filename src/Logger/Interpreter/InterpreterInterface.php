<?php
namespace Athena\Logger\Interpreter;

use Athena\Logger\Structure\LoggerStructureNode;
use Athena\Stream\OutputStreamInterface;

interface InterpreterInterface
{
    /**
     * @param array $structure
     *
     * @return $string
     */
    public function interpret(array $structure);
}

