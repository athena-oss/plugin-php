<?php
namespace Athena\Api;

use Athena\Api\Request\FluentRequestInterface;

interface ClientInterface
{
    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function get($uri);

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function post($uri);

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function put($uri);

    /**
     * @param $uri
     * @return FluentRequestInterface
     */
    public function delete($uri);
}

