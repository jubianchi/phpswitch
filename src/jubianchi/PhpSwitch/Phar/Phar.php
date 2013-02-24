<?php
namespace jubianchi\PhpSwitch\Phar;

class Phar
{
    protected $packager;
    protected $archive;

    public function __construct(\Phar $archive, Packager $packager = null)
    {
        $this->archive = $archive;
        $this->packager = $packager ?: new Packager();
    }

    public function getArchive()
    {
        return $this->archive;
    }

    public function getPackager()
    {
        return $this->packager;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->getArchive(), $method), $args);
    }

    public function addFile($file, $localname, $filter = true)
    {
        $content = file_get_contents($file);

        return $this->addFromString($localname, $content, $filter);
    }

    public function addFromString($localname, $contents, $filter = true)
    {
        if ($filter) {
            $contents = $this->getPackager()->package($contents);
        }

        $this->getArchive()->addFromString($localname, $contents);

        return $this;
    }
}
