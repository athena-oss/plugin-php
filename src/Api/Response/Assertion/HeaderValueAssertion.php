<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;

class HeaderValueAssertion implements ResponseAssertionInterface
{
    /**
     * @var string
     */
    private $expectedHeaderName;

    /**
     * @var string
     */
    private $expectedHeaderValue;

    /**
     * HeaderAssertion constructor.
     * @param $expectedHeaderName
     * @param $expectedHeaderValue
     */
    public function __construct($expectedHeaderName, $expectedHeaderValue)
    {
        $this->expectedHeaderName = $expectedHeaderName;
        $this->expectedHeaderValue = $expectedHeaderValue;
    }

    /**
     * @param ResponseHolder $response
     * @return bool
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        (new HeaderAssertion($this->expectedHeaderName))->assert($response);

        if (($headerValue = $response->getHeader($this->expectedHeaderName)) !== $this->expectedHeaderValue) {
            $message = sprintf('Header %s value [%s] is not as expected [%s]', $this->expectedHeaderName, $headerValue, $this->expectedHeaderValue);
            throw new UnexpectedValueException($message);
        }
        return true;
    }
}

