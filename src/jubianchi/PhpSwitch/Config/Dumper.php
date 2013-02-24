<?php
namespace jubianchi\PhpSwitch\Config;

use Symfony\Component\Yaml\Yaml;

class Dumper
{
    const GLOBAL_DIR = 0;
    const LOCAL_DIR = 1;

    /** @var string[] */
    private $directories;

    /**
     * @param string[] $directories
     */
    public function __construct(array $directories)
    {
        $this->directories = $directories;
    }

    /**
     * @param string                                    $name
     * @param \jubianchi\PhpSwitch\Config\Configuration $configuration
     * @param int                                       $directory
     *
     * @return \jubianchi\PhpSwitch\Config\Dumper
     */
    public function dump($name, Configuration $configuration, $directory = self::GLOBAL_DIR)
    {
        $path = $this->directories[$directory] . DIRECTORY_SEPARATOR . $name;

        file_put_contents(
            $path,
            Yaml::dump(array(Configuration::ROOT => $configuration->getValues()), 2, 2)
        );

        return $this;
    }
}
