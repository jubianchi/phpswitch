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

use jubianchi\PhpSwitch\PHP\Option\With\ApacheOption;
use jubianchi\PhpSwitch\PHP\Option\With\Apxs2Option;
use jubianchi\PhpSwitch\PHP\Option\With\ApxsOption;
use Symfony\Component\Process\Exception\ProcessFailedException;
use jubianchi\PhpSwitch\PHP\Option\OptionCollection;
use jubianchi\PhpSwitch\Process\Builder as ProcessBuilder;
use jubianchi\PhpSwitch\Event\Emitter;
use jubianchi\PhpSwitch\Event\Dispatcher;

class Builder extends Emitter
{
    /** @var string */
    private $directory;

    /** @var \jubianchi\PhpSwitch\Process\Builder\Factory */
    private $builder;

    /**
     * @param string                                        $directory
     * @param \jubianchi\PhpSwitch\Process\Builder\Factory  $builder
     * @param \jubianchi\PhpSwitch\Event\Dispatcher         $dispatcher
     */
    public function __construct($directory, ProcessBuilder\Factory $builder = null, Dispatcher $dispatcher = null)
    {
        $this->directory = $directory;
        $this->builder = $builder ?: new ProcessBuilder\Factory();

        if (null !== $dispatcher) {
            $this->setDispatcher($dispatcher);
        }
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                 $version
     * @param string                                           $source
     * @param \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options
     * @param int                                              $jobs
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function build(Version $version, $source, OptionCollection $options, $jobs = null)
    {
        $this->emit(
            'build.before',
            $args = array(
                'version' => $version,
                'source' => $source,
                'option' => $options,
                'jobs' => $jobs,
                'prefix' => $this->getDestination($version)
            )
        );

        $self = $this;
        $callback = function($type, $buffer) use ($self) {
            $buffer = rtrim($buffer);
            if (false === empty($buffer)) {
                $self->emit(
                    'build.progress',
                    array(
                        'type' => $type,
                        'buffer' => $buffer,
                    )
                );
            }
        };

        $this
            ->clean($source, $callback)
            ->configure($version, $source, $options, $callback)
            ->make($version, $source, $options, $jobs, $callback)
        ;

        $this->emit('build.after', $args);

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
    public function clean($source, $callback = null)
    {
        if (true === file_exists($this->directory . DIRECTORY_SEPARATOR . 'Makefile')) {
            $process = $this->builder->create(array('make', 'clean'))
                ->setWorkingDirectory($source)
                ->getProcess()
            ;

            $process->run($callback);

            if (false === $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }



        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                 $version
     * @param string                                           $source
     * @param \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options
     * @param callable                                         $callback
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function configure(Version $version, $source, OptionCollection $options, $callback = null)
    {
        $prefix = $this->getDestination($version);

        if (null !== $callback) {
            $callback('init', $prefix);
        }

        $builder = $this->builder
            ->create(
                array(
                    './configure',
                    '--prefix=' . $prefix,
                    '--with-config-file-path=' . $prefix . '/etc',
                    '--with-config-file-scan-dir=' . $prefix . '/var/db',
                    '--with-pear=' . $prefix . '/lib/php'
                )
            )
            ->setTimeout(null)
            ->setWorkingDirectory($source)
        ;

        foreach ($options as $option) {
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
     * @param \jubianchi\PhpSwitch\PHP\Version                 $version
     * @param string                                           $source
     * @param \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options
     * @param int                                              $jobs
     * @param callable                                         $callback
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function make(Version $version, $source, OptionCollection $options, $jobs = null, $callback = null)
    {
        $builder = $this->builder
            ->create(array('make'))
            ->setTimeout(null)
            ->setWorkingDirectory($source)
        ;

        if (null !== $jobs) {
            $builder->add('-j' . (int) $jobs);
        }

        $process = $builder->getProcess();
        $process->run($callback);
        if (false === $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }


        $root = $options->contains(ApacheOption::ARG)
            || $options->contains(ApxsOption::ARG)
            || $options->contains(Apxs2Option::ARG);

        $process = $this->builder
            ->create(array('make', 'install'))
            ->setRoot($root)
            ->setTimeout(null)
            ->setWorkingDirectory($source)
            ->getProcess();

        $process->run($callback);

        if(true === $root) {
            $this->builder
                ->create(array('chown', '-R', posix_geteuid() . ':' . posix_getgid(), $this->getDestination($version)))
                ->setRoot()
                ->setTimeout(null)
                ->setWorkingDirectory($source)
                ->getProcess()
                ->run($callback);
        }

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
        return $this->directory . DIRECTORY_SEPARATOR . $version;
    }
}
