<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Phar;

use Symfony\Component\Finder\Finder;

class Builder implements \Countable
{
    protected $name;
    protected $basedir;
    protected $finders = array();
    protected $filters;
    protected $raw = array();
    protected $stub;

    public function __construct(Filter\FilterCollection $filters = null, array $files = array())
    {
        $this->filters = $filters ?: new Filter\FilterCollection();
        $this->finders['files'] = new \ArrayObject($files);
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setBasedir($basedir)
    {
        $this->basedir = realpath($basedir);

        return $this;
    }

    public function addFinder(Finder $finder)
    {
        $this->finders[] = $finder;

        return $this;
    }

    public function addFile($file)
    {
        if (false === file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('File %s does not exist', $file));
        }

        $this->finders['files'][] = $file;

        return $this;
    }

    public function addRaw($name, $raw)
    {
        $this->raw[$name] = $raw;

        return $this;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;

        return $this;
    }

    public function count()
    {
        $count = count($this->raw);

        foreach ($this->finders as $finder) {
            $count += count($finder);
        }

        return $count;
    }

    public function getPhar($name)
    {
        return new \Phar($name);
    }

    /**
     * @param callable $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return \Phar
     */
    public function buildPhar($callback = null)
    {
        if (null !== $callback && false === is_callable($callback)) {
            throw new \InvalidArgumentException('Callback is not callable');
        }

        $phar = $this->getPhar($this->name);

        $phar->startBuffering();

        $total = count($this);
        $current = $previous = 0;

        if (null !== $callback) {
            $callback($total, $current, $previous);
        }

        foreach ($this->finders as $finder) {
            foreach ($finder as $file) {
                $file = is_string($file) ? new \SplFileObject($file) : $file;
                $path = $file->getRealPath() ?: $file->getPathname();
                $contents = file_get_contents($path);
                $contents = $this->filters->apply($contents);

                $phar->addFromString(
                    str_replace(
                        realpath($this->basedir) . DIRECTORY_SEPARATOR,
                        '',
                        $path
                    ),
                    $contents
                );

                if (null !== $callback) {
                    $callback($total, ++$current, $previous);
                }

                $previous = $current;
            }
        }

        foreach ($this->raw as $file => $raw) {
            $phar->addFromString($file, $raw);

            if (null !== $callback) {
                $callback($total, ++$current, $previous);
            }

            $previous = $current;
        }

        if (null !== $this->stub) {
            $phar->setStub($this->stub);
        }

        $phar->stopBuffering();

        return $phar;
    }
}
