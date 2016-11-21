<?php
namespace Athena\Browser;

use Athena\Athena;
use OLX\FluentWebDriverClient\Browser\BrowserInterface;

class CurrentActiveBrowser
{
    private $resetBrowser;

    /**
     * @var BrowserInterface
     */
    private $browser;

    /**
     * CurrentActiveBrowser constructor.
     * @param $resetBrowser
     */
    public function __construct($resetBrowser)
    {
        $this->resetBrowser = $resetBrowser;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $this->browser = Athena::browser($this->resetBrowser);
        try {
            $res = call_user_func_array([$this->browser, $name], $arguments);
            return $res;
        } catch (\Exception $e) {
            Athena::getInstance()->setBrowser(null);
            throw $e;
        }
    }
}
