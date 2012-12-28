<?php
namespace jubianchi\PhpSwitch\PHP;

use Symfony\Component\DomCrawler\Crawler;

class Finder implements \IteratorAggregate
{
    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $crawler;

    /** @var \Symfony\Component\DomCrawler\Crawler */
    private $items;

    /**
     * @param string                                $url
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     */
    public function __construct($url = 'http://php.net/releases/', Crawler $crawler = null)
    {
        if (null === $crawler) {
            $this->crawler = new Crawler();
        }

        $this->crawler->addContent(file_get_contents($url));
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

        foreach ($this->crawl() as $elem) {
            $value = $elem->nodeValue;

            if (false != preg_match('/^(PHP\s*((5\.\d+)\.\d+)).*tar\.bz2.*/', $value, $matches)) {
                $name = 'php-' . $matches[2];
                $versions[$name] = new Version($name, $elem->getAttribute('href'));
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

    /**
     * @param bool $refresh
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function crawl($refresh = false)
    {
        if (null === $this->items || true === $refresh) {
            $this->items = $this->crawler->filter('body #content li a');
        }

        return $this->items;
    }
}
