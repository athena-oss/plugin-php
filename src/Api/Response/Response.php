<?php
namespace Athena\Api\Response;

use Athena\Api\Response\Decorator\ResponseWithAssertions;
use Athena\Holder\ClosureHolder;

class Response implements ResponseInterface
{
    /**
     * @var ClosureHolder
     */
    private $closureHolder;

    private $responseHolder;

    /**
     * FluentResponse constructor.
     * @param ClosureHolder $requestExecutor
     */
    public function __construct(ClosureHolder $requestExecutor)
    {
        $this->closureHolder = $requestExecutor;
    }

    /**
     * @return ResponseWithAssertions
     */
    public function assertThat()
    {
        return new ResponseWithAssertions($this);
    }

    /**
     * @return ResponseFormatterInterface
     */
    public function retrieve()
    {
        return new ResponseFormatter($this->getResponseHolder());
    }

    /**
     * @return ClosureHolder
     */
    public function getClosureHolder()
    {
        return $this->closureHolder;
    }

    /**
     * @return ResponseHolder
     */
    public function getResponseHolder()
    {
        if (is_null($this->responseHolder)) {
            $response = $this->closureHolder->get();
            $this->responseHolder = new ResponseHolder($response);
        }
        return $this->responseHolder;
    }
}

