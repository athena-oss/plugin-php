<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use Peekmo\JsonPath\JsonPath;

class JsonPathValueAssertion implements ResponseAssertionInterface
{

    private $path;
    private $value;
    private $jsonPath;

    public function __construct($path, $value)
    {
        $this->path = $path;
        $this->value = $value;
        $this->jsonPath = new JsonPath();
    }

    /**
     * @param ResponseHolder $response
     * @return bool
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        (new JsonPathAssertion($this->path))->assert($response);

        $json = json_decode($response->getBody(), true);
        $match = $this->jsonPath->jsonPath($json, $this->path);

        if ($this->value !== $match[0]) {
            throw new UnexpectedValueException(
                sprintf(
                    "JSON path %s value [%s] is not as expected [%s] in JSON: \n%s",
                    $this->path,
                    json_encode($match, JSON_PRETTY_PRINT),
                    var_export($this->value, true),
                    json_encode($json, JSON_PRETTY_PRINT)
                )
            );
        }
        return true;
    }
}

