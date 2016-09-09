<?php
namespace Athena\Proxy;

use Athena\Exception\NotFoundException;

class ProxyAssertions implements ProxyAssertionsInterface
{
    /**
     * @var \Athena\Proxy\BrowserProxyClient
     */
    private $proxyClient;

    /**
     * ProxyAssertions constructor.
     *
     * @param \Athena\Proxy\BrowserProxyClient $proxyClient
     */
    public function __construct(BrowserProxyClient $proxyClient)
    {
        $this->proxyClient = $proxyClient;
    }

    /**
     * @param array $regexStrList
     *
     * @return bool
     * @throws \Athena\Exception\NotFoundException
     */
    public function existsInAllRequests(array $regexStrList)
    {
        $requestedUrlsList = $this->proxyClient->getAllRequestedUrls();

        foreach ($regexStrList as $regexStr) {

            $invalidMatches = preg_grep($regexStr, $requestedUrlsList, PREG_GREP_INVERT);

            if (count($invalidMatches) > 0) {
                throw new NotFoundException(
                    sprintf(
                        "Failed to find given '%s' regex string in the following requested urls:\n%s",
                        $regexStr,
                        implode($invalidMatches, "\n"))
                );
            }
        }

        return true;
    }

    /**
     * @param array $regexStrList
     *
     * @return bool
     * @throws \Athena\Exception\NotFoundException
     */
    public function existsInAtLeastOneRequest(array $regexStrList)
    {
        $requestedUrlsList = $this->proxyClient->getAllRequestedUrls();

        foreach ($regexStrList as $regexStr) {
            if (count(preg_grep($regexStr, $requestedUrlsList)) > 0) {
                return true;
            }
        }

        throw new NotFoundException("None of the requested URLs match the provided regex strings.");
    }
}

