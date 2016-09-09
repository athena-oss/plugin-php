<?php
namespace Athena\Api\Response;

interface ResponseFormatterInterface
{
    /**
     * @param bool $asAssociativeArray
     * @return array
     */
    public function fromJson($asAssociativeArray = true);

    /**
     * @return string
     */
    public function fromString();

    /**
     * @return ResponseHolder
     */
    public function getResponse();
}

