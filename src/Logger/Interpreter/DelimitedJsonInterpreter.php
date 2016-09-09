<?php
namespace Athena\Logger\Interpreter;

use Athena\Exception\InvalidJsonStringException;

class DelimitedJsonInterpreter implements InterpreterInterface
{
    const NEW_LINE = "\n";

    /**
     * @var string
     */
    private $delimiter;

    /**
     * DelimitedJsonPrinter constructor.
     *
     * @param string $delimiter
     */
    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param array|\Athena\Logger\Structure\LoggerStructureNode $structure
     * @return
     * @throws InvalidJsonStringException
     */
    public function interpret(array $structure)
    {
        $jsonContent = json_encode($structure);

        if ($jsonContent === false) {
            throw new InvalidJsonStringException(
                sprintf("Failed decoding json from input stream, with the following error: %s.", json_last_error_msg())
            );
        }

        return sprintf("%s%s", $jsonContent, $this->delimiter);
    }
}

