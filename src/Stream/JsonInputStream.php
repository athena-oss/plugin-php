<?php
namespace Athena\Stream;

use JsonSchema\Exception\JsonDecodingException;

class JsonInputStream implements InputStreamInterface
{
    /**
     * @var \Athena\Stream\InputStreamInterface
     */
    private $inputStream;

    /**
     * JsonInputStream constructor.
     *
     * @param \Athena\Stream\InputStreamInterface $inputStream
     *
     */
    public function __construct(InputStreamInterface $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->inputStream->valid();
    }

    /**
     * @return string
     */
    public function read()
    {
        $jsonContent = json_decode($this->inputStream->read(), true);

        if ($jsonContent === false) {
            throw new JsonDecodingException(
                sprintf("Failed decoding json from input stream, with the following error: %s.", json_last_error_msg())
            );
        }

        return $jsonContent;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return $this->inputStream->close();
    }
}

