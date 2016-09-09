<?php
namespace Athena\Page;

use Athena\Athena;

class BaseApiPage extends AbstractApiPage
{
    /**
     * AbstractApiPage constructor.
     */
    public function __construct()
    {
        parent::__construct(Athena::api());
    }
}

