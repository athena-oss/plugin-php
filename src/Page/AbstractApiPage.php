<?php
namespace Athena\Page;

use Athena\Api\ClientInterface;

class AbstractApiPage
{
    /**
     * @var ClientInterface
     */
    private $apiClient;

    /**
     * AbstractApiPage constructor.
     * @param ClientInterface $apiClient
     */
    public function __construct(ClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return ClientInterface
     */
    protected function client()
    {
        return $this->apiClient;
    }
}

