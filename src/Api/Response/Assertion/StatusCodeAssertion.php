<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;

class StatusCodeAssertion implements ResponseAssertionInterface
{
    /**
     * @var int
     */
    private $expectedStatusCode;

    /**
     * StatusCodeAssertion constructor.
     * @param int $expectedStatusCode
     */
    public function __construct($expectedStatusCode)
    {
        $this->expectedStatusCode = $expectedStatusCode;
    }

    /**
     * @param ResponseHolder $response
     * @return bool
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        if (($statusCode = $response->getStatusCode()) !== $this->expectedStatusCode) {
            $message = sprintf('HTTP Status Code [%d] is not as expected [%d]', $statusCode, $this->expectedStatusCode);
            throw new UnexpectedValueException($message);
        }
        return true;
    }
}

