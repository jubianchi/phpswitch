<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Finder\Finder as BaseFinder;
use Symfony\Component\Finder\Adapter\PhpAdapter;

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
            ->removeAdapters()
            ->addAdapter(new PhpAdapter())
            ->files()
            ->in($directory)
            ->name('*Option.php')
        ;
    }

    public function getIterator()
    {
        return new Iterator(parent::getIterator(), $this->basedir);
    }
}
