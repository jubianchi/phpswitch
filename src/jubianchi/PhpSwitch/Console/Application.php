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

    /** @var \jubianchi\PhpSwitch\PHP\Downloader */
    private $downloader;

    /** @var \jubianchi\PhpSwitch\PHP\Extracter */
    private $extracter;

    /** @var \jubianchi\PhpSwitch\PHP\Builder */
    private $builder;

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'PhpSwitch', $version = '0.1')
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
        if (null === $this->downloader) {
            $this->downloader = new Downloader($this->getConfiguration()->get('downloads'));
        }

        return $this->downloader;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function getExtracter()
    {
        if (null === $this->extracter) {
            $this->extracter = new Extracter($this->getConfiguration()->get('sources'));
        }

        return $this->extracter;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function getBuilder()
    {
        if (null === $this->builder) {
            $this->builder = new Builder($this->getConfiguration()->get('install'));
        }

        return $this->builder;
    }
}
