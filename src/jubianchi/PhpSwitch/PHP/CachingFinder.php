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

class CachingFinder extends Finder
{
    /** @var  string */
    private $cache;

    /**
     * @param array                                      $cache
     * @param array                                      $sites
     * @param \Symfony\Component\DomCrawler\Crawler|null $crawler
     * @param \jubianchi\PhpSwitch\Event\Dispatcher      $dispatcher
     */
    public function __construct($cache, array $sites, Crawler $crawler = null, Dispatcher $dispatcher = null)
    {
        parent::__construct($sites, $crawler, $dispatcher);

        $this->cache = $cache;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Version[]
     */
    protected function getVersions()
    {
        $versions = parent::getVersions();
        $cache = array();

        foreach ($versions as $number => $version) {
            if (false === array_key_exists($number, $cache)) {
                $cache[$number] = $version->getUrl();
            }
        }

        file_put_contents($this->cache, json_encode($cache));

        return $versions;
    }
}
