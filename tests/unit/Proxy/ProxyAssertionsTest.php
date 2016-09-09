<?php

namespace Athena\Tests\Proxy;

use Athena\Proxy\BrowserProxyClient;
use Athena\Proxy\ProxyAssertions;

class ProxyAssertionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Athena\Proxy\ProxyAssertions
     */
    private $proxyAssertionsObj;

    protected function setUp()
    {
        $fakeProxyClient = \Phake::mock(BrowserProxyClient::class);

        \Phake::when($fakeProxyClient)->getAllRequestedUrls()->thenReturn([
            "http://www.google.com/?a=1",
            "http://www.google.com/?a=1&b=2"
        ]);

        $this->proxyAssertionsObj = new ProxyAssertions($fakeProxyClient);
    }

    public function testExistsInAllRequests_RegexStrExistsInAllRequests_ShouldReturnTrue()
    {
        $regexStrList = [];
        $regexStrList[] = '/a=[0-9]+/';

        $this->assertTrue($this->proxyAssertionsObj->existsInAllRequests($regexStrList));
    }

    /**
     * @expectedException \Athena\Exception\NotFoundException
     */
    public function testExistsInAllRequests_RegexStrExistsInOnlyOneRequest_ShouldThrowNotFoundException()
    {
        $regexStrList = [];
        $regexStrList[] = '/b=[0-9]+/';

        $this->proxyAssertionsObj->existsInAllRequests($regexStrList);
    }

    public function testExistsInAtLeastOneRequest_RegexStrExistsInOnlyOneRequest_ShouldReturnTrue()
    {
        $regexStrList = [];
        $regexStrList[] = '/b=[0-9]+/';

        $this->assertTrue($this->proxyAssertionsObj->existsInAtLeastOneRequest($regexStrList));
    }

    public function testExistsInAtLeastOneRequest_RegexStrExistsInAllRequests_ShouldReturnTrue()
    {
        $regexStrList = [];
        $regexStrList[] = '/a=[0-9]+/';

        $this->assertTrue($this->proxyAssertionsObj->existsInAtLeastOneRequest($regexStrList));
    }

    /**
     * @expectedException \Athena\Exception\NotFoundException
     */
    public function testExistsInAtLeastOneRequest_RegexStrDoesNotExistInAnyRequest_ShouldThrowNotFoundException()
    {
        $regexStrList = [];
        $regexStrList[] = '/c=[0-9]+/';

        $this->assertTrue($this->proxyAssertionsObj->existsInAtLeastOneRequest($regexStrList));
    }
}