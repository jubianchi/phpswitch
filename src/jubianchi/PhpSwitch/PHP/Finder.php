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
use Symfony\Component\DomCrawler\Crawler;

class Finder extends Emitter implements \IteratorAggregate
{
    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $crawler;

    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $sites;

    /**
     * @param array                                      $sites
     * @param \Symfony\Component\DomCrawler\Crawler|null $crawler
     * @param \jubianchi\PhpSwitch\Event\Dispatcher      $dispatcher
     */
    public function __construct(array $sites, Crawler $crawler = null, Dispatcher $dispatcher = null)
    {
        $this->sites = $sites;
        $this->crawler = null === $crawler ? new Crawler() : $crawler;
        $this->setDispatcher(null === $dispatcher ? new Dispatcher() : $dispatcher);
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
        $versions = array();

        foreach ($this->sites as $url => $regex) {
            $this->crawler->clear();

            $this->emit('fetch.start', array('url' => $url));

            $context = stream_context_create(array('http' => array('timeout' => 5)));

            if ($content = @file_get_contents($url, false, $context)) {
                $this->crawler->addContent($content);

                $this->emit('fetch.parsing', array('url' => $url));

                foreach ($this->crawler->filter('a') as $elem) {
                    $value = $elem->nodeValue;

                    if (false != preg_match($regex, $value, $matches)) {
                        $href = $elem->getAttribute('href');
                        $url = rtrim($url, '/');

                        if (false == preg_match('/^https?:\/\//', $href)) {
                            if (0 === strpos($href, '/')) {
                                $parts = parse_url($url);
                                $url = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
                            }

                            $href = $url . '/' . ltrim($href, '/');
                        }

                        $version = $matches[2];
                        if (false === array_key_exists($version, $versions)) {
                            $versions[$version] = new Version($version, $href);
                        }
                    }
                }

                $this->emit('fetch.end', array('url' => $url));
            } else {
                $this->emit('fetch.failed', array('url' => $url));
            }
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
}
