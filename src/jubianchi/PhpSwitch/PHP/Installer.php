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

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Option\OptionCollection;
use jubianchi\PhpSwitch\Event\Emitter;
use jubianchi\PhpSwitch\Event\Dispatcher;

class Installer extends Emitter
{
    /** @var \jubianchi\PhpSwitch\PHP\Builder */
    private $builder;

    /** @var \jubianchi\PhpSwitch\PHP\Downloader */
    private $downloader;

    /** @var \jubianchi\PhpSwitch\PHP\Extracter */
    private $extracter;

    /**
     * @param \jubianchi\PhpSwitch\PHP\Downloader   $downloader
     * @param \jubianchi\PhpSwitch\PHP\Extracter    $extracter
     * @param \jubianchi\PhpSwitch\PHP\Builder      $builder
     * @param \jubianchi\PhpSwitch\Event\Dispatcher $dispatcher
     */
    public function __construct(Downloader $downloader, Extracter $extracter, Builder $builder, Dispatcher $dispatcher = null)
    {
        $this->downloader = $downloader;
        $this->extracter = $extracter;
        $this->builder = $builder;

        if (null !== $dispatcher) {
            $this->setDispatcher($dispatcher);
        }
    }

    public function install(Template $template, $mirror, $jobs, InputInterface $input, OutputInterface $output)
    {
        $version = $template->getVersion();
        $options = $template->getOptions();

        $dest = $this->builder->getDestination($version);
        $this->emit(
            'install.before',
            $args = array(
                'version' => $version,
                'mirror' => $mirror,
                'jobs' => $jobs,
                'options' => $options,
                'destination' => $dest
            )
        );

        if ($this->isInstalled($version)) {
            throw new \RuntimeException(sprintf('PHP version %s is already installed', $version));
        }

        if (null !== $options) {
            $options->preInstall($version, $input, $output);
        }

        $archive = $this->download($version, $mirror);
        $source = $this->extract($version, $archive);
        $this->make($version, $source, $options, $jobs);

        if (null !== $options) {
            $options->postInstall($version, $input, $output);
        }

        $this->emit('install.after', $args);

        return $this;
    }

    public function isInstalled(Version $version)
    {
        return is_dir($this->builder->getDestination($version));
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $mirror
     *
     * @return \jubianchi\PhpSwitch\PHP\Installer
     */
    protected function download(Version $version, $mirror)
    {
        $archive = $this->downloader->getDestination($version);

        if (false === file_exists($archive)) {
            $this->downloader->download($version, $mirror);
        }

        return $archive;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $archive
     *
     * @return \jubianchi\PhpSwitch\PHP\Installer
     */
    protected function extract(Version $version, $archive)
    {
        $source = $this->extracter->getDestination($version);

        if (false === file_exists($source)) {
            $this->extracter->extract($version, $archive);
        }

        return $source;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                 $version
     * @param string                                           $source
     * @param \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options
     * @param int                                              $jobs
     *
     * @throws \RuntimeException
     *
     * @return \jubianchi\PhpSwitch\PHP\Installer
     */
    protected function make(Version $version, $source, OptionCollection $options, $jobs)
    {
        $prefix = $this->builder->getDestination($version);
        $this->builder->build($version, $source, $options, $jobs);
        mkdir($prefix . '/var/db', 0755, true);

        $ini = $source . DIRECTORY_SEPARATOR . 'php.ini-development';
        $destination = $prefix . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'php.ini';

        if (false === is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (is_file($ini)) {
            copy($ini, $destination);
        }

        return $prefix;
    }
}
