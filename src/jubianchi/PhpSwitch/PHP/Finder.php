<?php
namespace jubianchi\PhpSwitch\PHP;

use Symfony\Component\DomCrawler\Crawler;

class Finder implements \IteratorAggregate
{
    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $crawler;

    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $sites;

    /**
     * @param array                                      $sites
     * @param \Symfony\Component\DomCrawler\Crawler|null $crawler
     */
    public function __construct(array $sites, Crawler $crawler = null)
    {
        if (null === $crawler) {
            $this->crawler = new Crawler();
        }

        $this->sites = $sites;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getVersions());
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Version[]
     */
    protected function getVersions()
    {
        $versions = array();

        foreach ($this->sites as $url => $regex) {
            $this->crawler->clear();
            $this->crawler->addContent(file_get_contents($url));

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
