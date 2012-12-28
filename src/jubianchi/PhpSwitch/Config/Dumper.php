<?php
namespace jubianchi\PhpSwitch\Config;

use Symfony\Component\Yaml\Yaml;

class Dumper
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
     * @param string                                    $name
     * @param \jubianchi\PhpSwitch\Config\Configuration $configuration
	 *
	 * @return \jubianchi\PhpSwitch\Config\Dumper
     */
	public function dump($name, Configuration $configuration)
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . $name;

        file_put_contents(
            $path,
            Yaml::dump(array(Configuration::ROOT => $configuration->getValues()))
        );

        return $this;
    }
}
