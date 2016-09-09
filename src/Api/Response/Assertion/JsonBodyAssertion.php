<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;

class JsonBodyAssertion implements ResponseAssertionInterface
{
    /**
     * @param ResponseHolder $response
     * @return bool
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        $responseBody = $response->getBody();
        if (is_null(json_decode($responseBody))) {
            throw new UnexpectedValueException(
                sprintf("BODY Is not of expected type JSON. %s.\n\nActual output:\n\n%s", json_last_error_msg(), $responseBody)
            );
        }
        return true;
    }
}

