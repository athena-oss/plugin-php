<?php
namespace Athena\Proxy;

use Athena\Exception\NotFoundException;

interface ProxyAssertionsInterface
{
    /**
     * @param array $regexStrList
     * @return boolean
     * @throws NotFoundException
     */
    public function existsInAllRequests(array $regexStrList);

    /**
     * @param array $regexStrList
     * @return boolean
     * @throws NotFoundException
     */
    public function existsInAtLeastOneRequest(array $regexStrList);
}

