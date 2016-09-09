<?php
namespace Athena\Event;

use Symfony\Component\EventDispatcher\Event;

class HttpTransactionCompleted extends Event
{
    const AFTER = "http_transaction.after";
    /**
     * @var string
     */
    private $request;
    /**
     * @var string
     */
    private $response;
    /**
     * @var string
     */
    private $requestUrl;
    /**
     * @var string
     */
    private $requestMethod;

    /**
     * HttpTransactionCompleted constructor.
     *
     * @param string $request
     * @param string $response
     * @param string $requestUrl
     * @param string $requestMethod
     */
    public function __construct($request, $response, $requestUrl, $requestMethod)
    {
        $this->request = $request;
        $this->response = $response;
        $this->requestUrl = $requestUrl;
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
}

