<?php

namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;
use PHPUnit_Framework_MockObject_MockObject;

class JsonPathValueAssertionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $responseHolder;

    public function setUp()
    {
        $bodyContents = json_encode(
            [
                'bar' => 'lalala',
                'foo' => 'lalala',
                'fooBar' => 'whatever',
                'baz' => [
                    'numberz' => 123,
                    'cha-ching' => 'lalala',
                    'bazinga' => 'lalala'
                ]
            ],
            JSON_PRETTY_PRINT
        );

        $this->responseHolder = $this->getMockWithoutInvokingTheOriginalConstructor(ResponseHolder::class);
        $this->responseHolder->method('getBody')->willReturn($bodyContents);
    }

    public function testAssertMatchingValue()
    {
        $path = '$.foo';
        $value = 'lalala';
        $this->assertTrue((new JsonPathValueAssertion($path, $value))->assert($this->responseHolder));
    }

    public function testAssertNotMatchingValue()
    {
        $path = '$.fooBar';
        $value = 'something else';

        $this->setExpectedException(UnexpectedValueException::class);
        (new JsonPathValueAssertion($path, $value))->assert($this->responseHolder);
    }

    public function testAssertPathNotFound()
    {
        $path = '$.path.not.found';
        $value = false;

        $this->setExpectedException(UnexpectedValueException::class);
        (new JsonPathValueAssertion($path, $value))->assert($this->responseHolder);
    }

}
