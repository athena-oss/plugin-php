<?php
namespace Athena\Api\Response;

use Athena\Api\Response\Assertion\JsonBodyAssertion;
use Peekmo\JsonPath\JsonPath;

class ResponseFormatter implements ResponseFormatterInterface
{
    private $response;

    /**
     * ResponseFormatter constructor.
     * @param ResponseHolder $response
     */
    public function __construct(ResponseHolder $response)
    {
        $this->response = $response;
    }

    /**
     * @param bool $asAssociativeArray
     * @return array
     */
    public function fromJson($asAssociativeArray = true)
    {
        (new JsonBodyAssertion())->assert($this->response);
        $content = json_decode($this->response->getBody(), $asAssociativeArray);
        return $content;
    }

    /**
     * JSONPath lib path modifiers reference: http://goessner.net/articles/JsonPath/
     *
     * Path example:
     * $.foo =
     * {
     *  "foo":"bar"
     * }
     *
     * $.foo.bar =
     * {
     *      "foo": {
     *          "bar":"baz"
     *      }
     * }
     * @param $path
     * @return $this
     */
    public function fromJsonPath($path)
    {
        return (new JsonPath())->jsonPath($this->fromJson(), $path);
    }

    /**
     * @return string
     */
    public function fromString()
    {
        return $this->response->getBody();
    }

    /**
     * @return ResponseHolder
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->response->getBody();
    }
}

