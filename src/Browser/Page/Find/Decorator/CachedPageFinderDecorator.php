<?php
namespace Athena\Browser\Page\Find\Decorator;

use Athena\Browser\BrowserInterface;
use Athena\Browser\Page\Find\PageFinderInterface;
use Facebook\WebDriver\Remote\RemoteWebElement;

class CachedPageFinderDecorator implements PageFinderInterface
{
    const BY_XPATH = 'ByXpath';
    const BY_CSS   = 'ByCss';
    const BY_ID    = 'ById';
    const BY_NAME  = 'ByName';
    /**
     * @var PageFinderInterface
     */
    private $pageFinder;

    /**
     * @var array
     */
    private $cache;

    /**
     * CachedPageFinderDecorator constructor.
     * @param PageFinderInterface $pageFinder
     */
    public function __construct(PageFinderInterface $pageFinder)
    {
        $this->pageFinder = $pageFinder;
        $this->cache      = [
            static::BY_NAME  => [],
            static::BY_ID    => [],
            static::BY_CSS   => [],
            static::BY_XPATH => []
        ];
    }

    /**
     * @param $name
     * @return RemoteWebElement
     */
    public function elementWithName($name)
    {
        return $this->get('elementWithName', $name, static::BY_NAME);
    }

    /**
     * @param $name
     * @return RemoteWebElement[]
     */
    public function elementsWithName($name)
    {
        return $this->get('elementsWithName', $name, static::BY_NAME);
    }

    /**
     * @param $id
     * @return RemoteWebElement
     */
    public function elementWithId($id)
    {
        return $this->get('elementWithId', $id, static::BY_ID);
    }

    /**
     * @param $id
     * @return RemoteWebElement[]
     */
    public function elementsWithId($id)
    {
        return $this->get('elementsWithId', $id, static::BY_ID);
    }

    /**
     * @param $css
     * @return RemoteWebElement
     */
    public function elementWithCss($css)
    {
        return $this->get('elementWithCss', $css, static::BY_CSS);
    }

    /**
     * @param $css
     * @return RemoteWebElement[]
     */
    public function elementsWithCss($css)
    {
        return $this->get('elementsWithCss', $css, static::BY_CSS);
    }

    /**
     * @param $xpath
     * @return RemoteWebElement
     */
    public function elementWithXpath($xpath)
    {
        return $this->get('elementWithXpath', $xpath, static::BY_XPATH);
    }

    /**
     * @param $xpath
     * @return RemoteWebElement[]
     */
    public function elementsWithXpath($xpath)
    {
        return $this->get('elementsWithXpath', $xpath, static::BY_XPATH);
    }

    /**
     * @param $method
     * @param $identifier
     * @param $bucket
     * @return RemoteWebElement
     */
    protected function get($method, $identifier, $bucket)
    {
        $key = $method . '_' . $identifier;
        if (array_key_exists($key, $this->cache[$bucket])) {
            return $this->cache[$bucket][$key];
        }
        $this->cache[$bucket][$key] = $this->pageFinder->$method($identifier);
        return $this->cache[$bucket][$key];
    }

    /**
     * @return BrowserInterface
     */
    public function getBrowser()
    {
        return $this->pageFinder->getBrowser();
    }
}

