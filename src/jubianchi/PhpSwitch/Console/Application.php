<?php
namespace jubianchi\PhpSwitch\Console;

use Symfony\Component\Console\Application as BaseApplication;
use jubianchi\PhpSwitch\PHP\Builder;
use jubianchi\PhpSwitch\PHP\Extracter;
use jubianchi\PhpSwitch\PHP\Downloader;
use jubianchi\PhpSwitch\Config\Configuration;

class Application extends BaseApplication
{
    /** @var \jubianchi\PhpSwitch\Config\Configuration */
    private $configuration;

    /** @var \Pimple */
    private $container;

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'phpswitch', $version = '0.1')
    {
        parent::__construct($name, $version);
    }

    /**
     * @param \jubianchi\PhpSwitch\Config\Configuration $configuration
     *
     * @return \jubianchi\PhpSwitch\Console\Application
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Downloader
     */
    public function getDownloader()
    {
        return $this->getService('app.php.downloader');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function getExtracter()
    {
        return $this->getService('app.php.extracter');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function getBuilder()
    {
        return $this->getService('app.php.builder');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Finder
     */
    public function getOptionFinder()
    {
        return $this->getService('app.php.option.finder');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Resolver
     */
    public function getOptionResolver()
    {
        return $this->getService('app.php.option.resolver');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Normalizer
     */
    public function getOptionNormalizer()
    {
        return $this->getService('app.php.option.normalizer');
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->getService('app.logger');
    }

    /**
     * @param \Pimple $container
     *
     * @return \jubianchi\PhpSwitch\Console\Application
     */
    public function setContainer(\Pimple $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return \Pimple
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $service
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function getService($service)
    {
        if (null === $this->container) {
            throw new \RuntimeException(sprintf('No service container defined'));
        }

        return $this->container[$service];
    }

    public function getParameter($name)
    {
        $parameters = $this->getService('parameters');

        return $parameters[$name];
    }
}
