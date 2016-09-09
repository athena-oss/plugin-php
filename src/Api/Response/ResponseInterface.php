<?php
namespace Athena\Api\Response;

interface ResponseInterface
{
    /**
     * @return ResponseFormatterInterface
     */
    public function retrieve();
}

