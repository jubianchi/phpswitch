<?php
namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Process\Builder as ProcessBuilder;
use jubianchi\PhpSwitch\Event\Emitter;
use jubianchi\PhpSwitch\Event\Dispatcher;
use jubianchi\PhpSwitch\Event\Event;

class Extracter extends Emitter
{
    /** @var string */
    private $directory;

    /** @var \jubianchi\PhpSwitch\Process\Builder */
    private $builder;

    /**
     * @param string                                $directory
     * @param \jubianchi\PhpSwitch\Process\Builder  $builder
     * @param \jubianchi\PhpSwitch\Event\Dispatcher $dispatcher
     */
    public function __construct($directory, ProcessBuilder $builder = null, Dispatcher $dispatcher = null)
    {
        $this->directory = $directory;
        $this->builder = $builder ?: new ProcessBuilder();

        if (null !== $dispatcher) {
            $this->setDispatcher($dispatcher);
        }
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $archive
     *
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function extract(Version $version, $archive)
    {
        $this->emit(
            'extract.before',
            $args = array(
                'version' => $version,
                'archive' => $archive
            )
        );

        $basename = Version::DEFAULT_NAME . '-' . $version->getVersion();
        $dirname = dirname($archive);

        $self = $this;
        $callback = function($type, $buffer) use($self) {
            $buffer = rtrim($buffer);
            if (false === empty($buffer)) {
                $self->emit(
                    'extract.progress',
                    array(
                        'type' => $type,
                        'buffer' => $buffer,
                    )
                );
            }
        };

        $this->builder->get()
            ->setWorkingDirectory($dirname)
            ->add('tar')
            ->add('-xvf')
            ->add($archive)
            ->getProcess()
                ->run($callback)
        ;

        $this->builder->get()
            ->setWorkingDirectory($dirname)
            ->add('mv')
            ->add('-f')
            ->add($basename)
            ->add($this->getDestination($version))
            ->getProcess()
                ->run($callback)
        ;

        $this->emit('extract.after', $args);

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
