<?php
namespace jubianchi\PhpSwitch\Console\Command;

use Symfony\Component\Finder\Finder as BaseFinder;

class Finder extends BaseFinder
{
    /** @var string */
    private $basedir;

    /**
     * @param string $directory
     * @param string $basedir
     */
    public function __construct($directory, $basedir)
    {
        parent::__construct();

        $this->basedir = $basedir;

        $this
            ->files()
            ->in($directory)
            ->name('*Command.php')
        ;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new Iterator(parent::getIterator(), $this->basedir);
    }
}
