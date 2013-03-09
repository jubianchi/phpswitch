<?php
namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Process\Builder as ProcessBuilder;

class Extracter
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
     * @param string                           $file
     * @param callable                         $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function extract(Version $version, $file, $callback = null)
    {
        $basename = Version::DEFAULT_NAME . '-' . $version->getVersion();
        $dirname = dirname($file);

        $this->builder->get()
            ->setWorkingDirectory($dirname)
            ->add('tar')
            ->add('-xvf')
            ->add($file)
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
