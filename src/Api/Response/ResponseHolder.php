<?php
namespace Athena\Api\Response;

use GuzzleHttp\Message\Response as GuzzleResponse;

class ResponseHolder
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var Response
     */
    private $response;

    /**
     * ResponseHolder constructor.
     * @param GuzzleResponse $response
     */
    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        if (is_null($this->body)) {
            $this->body = $this->response->getBody()->getContents();
        }
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (is_null($this->statusCode)) {
            $this->statusCode = $this->response->getStatusCode();
        }
        return $this->statusCode;
    }

    /**
     * @param string $headerName
     * @return string
     */
    public function getHeader($headerName)
    {
        if (!array_key_exists($headerName, $this->headers)) {
            $this->headers[$headerName] = $this->response->getHeader($headerName) ?: false;
        }

        return $this->headers[$headerName];
    }
}

