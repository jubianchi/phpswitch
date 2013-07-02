<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Event\Dispatcher;
use jubianchi\PhpSwitch\Event\Emitter;
use jubianchi\PhpSwitch\Event\Subscriber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Event;

class CachedFinder extends Finder
{
    /** @var  string */
    private $cache;

    /** @var \jubianchi\PhpSwitch\PHP\Finder */
    private $finder;

    /**
     * @param \jubianchi\PhpSwitch\PHP\Finder $finder
     * @param string                          $cache
     */
    public function __construct(Finder $finder, $cache)
    {
        $this->cache = $cache;
        $this->finder = $finder;
    }

    /**
     * @param callable $sort
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(\Closure $sort = null)
    {
        $items = $this->getVersions();
        if($sort !== null) {
            usort($items, $sort);
        }

        return new \ArrayIterator($items);
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Version[]
     */
    protected function getVersions()
    {
        if(is_file($this->cache)) {
            $versions = json_decode(file_get_contents($this->cache), true);

            foreach ($versions as $number => $url) {
                $versions[$number] = new Version($number, $url);
            }
        } else {
            $versions = $this->finder->getVersions();
        }

        return $versions;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \jubianchi\PhpSwitch\PHP\Version
     */
    public function getVersion($name)
    {
        $versions = $this->getVersions();

        if (false === array_key_exists($name, $versions)) {
            throw new \InvalidArgumentException(sprintf('Version %s does not exist', $name));
        }

        return $versions[$name];
    }

    public function setDispatcher(Dispatcher $dispatcher)
    {
        return $this->finder->setDispatcher($dispatcher);
    }

    public function emit($name, array $args = array())
    {
        return $this->finder->emit($name, $args);
    }

    public function subscribe(Subscriber $subscriber)
    {
        return $this->finder->subscribe($subscriber);
    }

    public function unsubscribe(Subscriber $subscriber)
    {
        return $this->finder->unsubscribe($subscriber);
    }
}
