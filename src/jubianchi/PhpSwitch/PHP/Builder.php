<?php
namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Process\Builder as ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Builder
{
    /** @var string */
    private $directory;

    /** @var \jubianchi\PhpSwitch\Process\Builder */
    private $builder;

    /**
     * @param string                               $directory
     * @param \jubianchi\PhpSwitch\Process\Builder $builder
     */
    public function __construct($directory, ProcessBuilder $builder = null)
    {
        $this->directory = $directory;
        $this->builder = $builder ?: new ProcessBuilder();
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $source
     * @param array                            $options
     * @param callable                         $callback
     */
    public function build(Version $version, $source, $options, $jobs = null, $callback = null)
    {
        $this
            ->clean($source, $callback)
            ->configure($version, $source, $options, $callback)
            ->make($source, $jobs, $callback)
        ;
    }

    /**
     * @param string   $source
     * @param callable $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function clean($source, $callback = null)
    {
        if (true === file_exists($this->directory . DIRECTORY_SEPARATOR . 'Makefile')) {
            $this->builder->get()
                ->setWorkingDirectory($source)
                ->add('make')
                ->add('clean')
                ->getProcess()
                    ->run($callback)
            ;
        }

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $source
     * @param array                            $options
     * @param callable                         $callback
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function configure(Version $version, $source, $options, $callback = null)
    {
        $prefix = $this->getDestination($version);
        $builder = $this->builder->get()
            ->setWorkingDirectory($source)
            ->add('./configure')
            ->add('--prefix=' . $prefix)
            ->add('--with-config-file-path=' . $prefix . '/etc')
            ->add('--with-config-file-scan-dir=' . $prefix . '/var/db')
            ->add('--with-pear=' . $prefix . '/lib/php')
        ;

        foreach(explode(' ', $options) as $option) {
            $builder->add((string) $option);
        }

        $process = $builder->getProcess();

        $process->run($callback);

        if (false === $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    /**
     * @param string   $source
     * @param callable $callback
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function make($source, $jobs = null, $callback = null)
    {
        $builder = $this->builder->get()
            ->setTimeout(null)
            ->setWorkingDirectory($source)
            ->add('make')
        ;

        if (null !== $jobs) {
            $builder->add('-j' . (int) $jobs);
        }

        $process = $builder->getProcess();
        $process->run($callback);
        if (false === $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $builder->add('install');
        $process = $builder->getProcess();
        $process->run($callback);
        if (false === $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     *
     * @return string
     */
    public function getDestination(Version $version)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $version->getName() . '-' . $version->getVersion();
    }
}
