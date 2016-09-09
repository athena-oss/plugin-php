<?php
namespace Athena\Api\Response\Decorator;

use Athena\Api\Response\Assertion\HeaderAssertion;
use Athena\Api\Response\Assertion\HeaderValueAssertion;
use Athena\Api\Response\Assertion\JsonBodyAssertion;
use Athena\Api\Response\Assertion\JsonPathAssertion;
use Athena\Api\Response\Assertion\JsonPathValueAssertion;
use Athena\Api\Response\Assertion\JsonStructureAssertion;
use Athena\Api\Response\Assertion\ResponseAssertionInterface;
use Athena\Api\Response\Assertion\StatusCodeAssertion;
use Athena\Api\Response\Response;
use Athena\Api\Response\ResponseFormatterInterface;
use Athena\Api\Response\ResponseInterface;

class ResponseWithAssertions implements ResponseInterface
{
    /**
     * @var ResponseAssertionInterface[]
     */
    private $responseAssertions;

    /**
     * @var Response
     */
    private $response;

    /**
     * ResponseWithAssertions constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->responseAssertions = [];
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function statusCodeIs($statusCode)
    {
        $this->responseAssertions[] = new StatusCodeAssertion($statusCode);
        return $this;
    }

    /**
     * @return $this
     */
    public function responseIsJson()
    {
        $this->responseAssertions[] = new JsonBodyAssertion();
        return $this;
    }

    /**
     * @param $headerName
     * @return $this
     */
    public function hasHeader($headerName)
    {
        $this->responseAssertions[] = new HeaderAssertion($headerName);
        return $this;
    }

    /**
     * @param $headerName
     * @param $headerValue
     * @return $this
     */
    public function headerValueIsEqual($headerName, $headerValue)
    {
        $this->responseAssertions[] = new HeaderValueAssertion($headerName, $headerValue);
        return $this;
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
    public function jsonHasPath($path)
    {
        $this->responseAssertions[] = new JsonPathAssertion($path);
        return $this;
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
     * @param $value
     * @return $this
     */
    public function jsonPathValueIsEqual($path, $value)
    {
        $this->responseAssertions[] = new JsonPathValueAssertion($path, $value);
        return $this;
    }

    /**
     * JSONPath lib path modifiers reference: http://goessner.net/articles/JsonPath/
     *
     * Structure example:
     * ['foo'=>'*']
     * {
     *  "foo":"bar"
     * }
     *
     * ['foo'=>['bar'=>'baz']]
     * {
     *      "foo": {
     *          "bar":"baz"
     *      },
     *      "fooBar":"optional"
     * }
     * @param $structure
     * @return $this
     */
    public function jsonStructureIs(array $structure)
    {
        $this->responseAssertions[] = new JsonStructureAssertion($structure);
        return $this;
    }

    /**
     * @return ResponseFormatterInterface
     */
    public function retrieve()
    {
        $requestResponse = $this->response->getResponseHolder();
        foreach ($this->responseAssertions as $validator) {
            $validator->assert($requestResponse);
        }
        return $this->response->retrieve();
    }
}

