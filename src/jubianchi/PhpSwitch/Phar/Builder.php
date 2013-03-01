<?php
namespace jubianchi\PhpSwitch\Phar;

use Symfony\Component\Finder\Finder;

class Builder implements \Countable
{
    protected $name;
    protected $basedir;
    protected $finders = array();
    protected $filters = array();
    protected $files = array();
    protected $raw = array();
    protected $stub;
    protected $callback;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setBasedir($basedir)
    {
        $this->basedir = $basedir;

        return $this;
    }

    public function addFinder(Finder $finder)
    {
        $this->finders[] = $finder;

        return $this;
    }

    public function addFilter($filter)
    {
        if (false === is_callable($filter)) {
            throw new \InvalidArgumentException('Filter is not callable');
        }

        if (false === in_array($filter, $this->filters)) {
            $this->filters[] = $filter;
        }

        return $this;
    }

    public function addFile($file)
    {
        $this->files[] = $file;

        return $this;
    }

    public function addRaw($name, $raw)
    {
        $this->raw[$name] = $raw;

        return $this;
    }

    public function count()
    {
        $count = count($this->files) + count($this->raw);

        foreach ($this->finders as $finder) {
            $count += count($finder);
        }

        return $count;
    }

    public function setCallback($callback)
    {
        if (false === is_callable($callback)) {
            throw new \InvalidArgumentException('Callback is not callable');
        }

        $this->callback = $callback;

        return $this;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;

        return $this;
    }

    public function getPhar()
    {
        $phar = new \Phar($this->name);

        $phar->startBuffering();

        $total = count($this);
        $current = $previous = 0;

        if (null !== ($callback = $this->callback)) {
            $callback($total, $current, $previous);
        }

        foreach ($this->finders as $finder) {
            foreach ($finder as $file) {
                $contents = file_get_contents($file->getRealPath());
                $contents = $this->filterContents($contents);

                $phar->addFromString(
                    str_replace(
                        realpath($this->basedir) . DIRECTORY_SEPARATOR,
                        '',
                        $file->getRealPath()
                    ),
                    $contents
                );

                if (null !== $callback) {
                    $callback($total, $current, $previous);
                }

                $previous = $current;
                $current++;
            }
        }

        foreach ($this->files as $file) {
            $contents = file_get_contents($file);
            $contents = $this->filterContents($contents);

            $phar->addFromString(
                str_replace(
                    realpath($this->basedir) . DIRECTORY_SEPARATOR,
                    '',
                    $file
                ),
                $contents
            );

            if (null !== $callback) {
                $callback($total, $current, $previous);
            }

            $previous = $current;
            $current++;
        }

        foreach ($this->raw as $file => $raw) {
            $phar->addFromString($file, $raw);

            if (null !== $callback) {
                $callback($total, $current, $previous);
            }

            $previous = $current;
            $current++;
        }

        $phar->setStub($this->stub);

        $phar->stopBuffering();

        return $phar;
    }

    protected function filterContents($contents)
    {
        if (!function_exists('token_get_all')) {
            return $contents;
        }

        if (0 === sizeof($tokens = @token_get_all($contents))) {
            return $contents;
        }

        foreach ($this->filters as $filter) {
            $contents = call_user_func_array($filter, array($contents, $tokens));
        }

        return $contents;
    }
}
