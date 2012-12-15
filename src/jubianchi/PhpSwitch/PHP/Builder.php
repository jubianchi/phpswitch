<?php
namespace jubianchi\PhpSwitch\PHP;

use Symfony\Component\Process\Process;

class Builder
{
    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $source
     * @param array                            $options
     * @param callable                         $callback
     */
    public function build(Version $version, $source, $options, $callback = null)
    {
        $this
            ->clean($version, $callback)
            ->configure($version, $source, $options, $callback)
            ->make($source, $callback)
        ;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param callable                         $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function clean(Version $version, $callback = null)
    {
        if (true === file_exists($this->directory . DIRECTORY_SEPARATOR . 'Makefile')) {
            $process = new Process('make clean', $version->getSourcePath());
            $process->run($callback);
        }

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $source
     * @param array                            $options
     * @param callable                         $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function configure(Version $version, $source, $options, $callback = null)
    {
        $prefix = $this->getDestination($version);
        $args[] = '--prefix=' . $prefix;
        $args[] = '--with-config-file-path=' . $prefix . '/etc';
        $args[] = '--with-config-file-scan-dir=' . $prefix . '/var/db';
        $args[] = '--with-pear=' . $prefix . '/lib/php';

        $process = new Process(
            './configure ' . implode(' ', $args) . ' ' . $options,
            $source
        );
        $process->run($callback);

        if (false === $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this;
    }

    /**
     * @param string   $source
     * @param callable $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function make($source, $callback = null)
    {
        $process = new Process('make', $source);
        $process->run($callback);

        if (false === $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $process = new Process('make install', $source);
        $process->run($callback);

        if (false === $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
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
        return $this->directory . DIRECTORY_SEPARATOR . $version->getName();
    }
}
