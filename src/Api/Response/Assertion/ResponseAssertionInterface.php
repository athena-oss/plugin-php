<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;

interface ResponseAssertionInterface
{
    /**
     * @param ResponseHolder $response
     * @return mixed
     */
    public function assert(ResponseHolder $response);
}

