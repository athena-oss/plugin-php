<?php
namespace Athena\Logger;

use Athena\Proxy\BrowserProxyClient;
use Athena\Stream\OutputStreamInterface;

class ProxyTrafficLogger implements TrafficLoggerInterface
{
    /**
     * @var \Athena\Proxy\BrowserProxyClient
     */
    private $proxy;
    /**
     * @var \Athena\Stream\OutputStreamInterface
     */
    private $outputStream;

    /**
     * ProxyTrafficLogger constructor.
     *
     * @param \Athena\Proxy\BrowserProxyClient     $proxy
     * @param \Athena\Stream\OutputStreamInterface $outputStream
     */
    public function __construct(BrowserProxyClient $proxy, OutputStreamInterface $outputStream)
    {
        $this->proxy = $proxy;
        $this->outputStream = $outputStream;
    }

    /**
     * @return void
     */
    public function start()
    {
        $this->proxy->startTrafficRecording('/');
    }

    /**
     * @return string
     */
    public function write()
    {
        return $this->outputStream->write($this->proxy->getHar()->getContents());
    }
}

