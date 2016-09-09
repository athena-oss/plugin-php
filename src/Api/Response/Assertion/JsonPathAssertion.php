<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use Peekmo\JsonPath\JsonPath;

class JsonPathAssertion implements ResponseAssertionInterface
{

    private $path;
    private $jsonPath;

    public function __construct($path)
    {
        $this->path = $path;
        $this->jsonPath = new JsonPath();
    }

    /**
     * @param ResponseHolder $response
     * @return bool
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        (new JsonBodyAssertion())->assert($response);

        $json = json_decode($response->getBody(), true);
        $match = $this->jsonPath->jsonPath($json, $this->path);

        if ($match === false) {
            throw new UnexpectedValueException(
                sprintf(
                    "JSON path [%s] was not found",
                    $this->path
                )
            );
        }
        return true;
    }
}

