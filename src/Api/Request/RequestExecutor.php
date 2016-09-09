<?php
namespace Athena\Api\Request;

use Athena\Exception\UnsupportedOperationException;
use GuzzleHttp\Client;

class RequestExecutor
{
    private $httpClient;
    private $options;
    private $uri;
    private $method;

    /**
     * RequestExecutor constructor.
     * @param Client $httpClient
     * @param $method
     * @param string $uri
     * @param array $options
     */
    public function __construct(Client $httpClient, $method, $uri, array $options)
    {
        $this->httpClient = $httpClient;
        $this->method     = strtoupper($method);
        $this->uri        = $uri;
        $this->options    = $options;
    }

    /**
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function __invoke()
    {
        return $this->execute();
    }

    /**
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     * @throws UnsupportedOperationException
     */
    public function execute()
    {
        $requestResponse = null;
        switch ($this->method) {
            case 'GET':
                $requestResponse = $this->httpClient->get($this->uri, $this->options);
                break;
            case 'POST':
                $requestResponse = $this->httpClient->post($this->uri, $this->options);
                break;
            case 'PUT':
                $requestResponse = $this->httpClient->put($this->uri, $this->options);
                break;
            case 'DELETE':
                $requestResponse = $this->httpClient->delete($this->uri, $this->options);
                break;
            default:
                throw new UnsupportedOperationException(sprintf('HTTP METHOD [%s] is not supported.', $this->method));
        }

        return $requestResponse;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}

