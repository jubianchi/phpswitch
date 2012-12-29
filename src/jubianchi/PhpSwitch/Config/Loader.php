<?php
namespace jubianchi\PhpSwitch\Config;

use Symfony\Component\Yaml\Yaml;

class Loader
{
    /** @var string */
    private $directory;

    /** @var \jubianchi\PhpSwitch\Config\Validator */
    private $validator;

    /**
     * @param string                                $directory
     * @param \jubianchi\PhpSwitch\Config\Validator $validator
     */
    public function __construct($directory, Validator $validator)
    {
        $this->directory = $directory;
        $this->validator = $validator;
    }

    /**
     * @param string                                    $name
     * @param \jubianchi\PhpSwitch\Config\Configuration $configuration
     * @param \jubianchi\PhpSwitch\Config\Dumper        $dumper
     *
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function load($name, Configuration $configuration, Dumper $dumper)
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . $name;

        if (false === is_file($path)) {
            $dumper->dump(
                $name,
                $configuration->setValues($this->validator->validate(array()))
            );
        }

        $configuration->setValues($this->validator->validate($this->parse($path)));
        $configuration->setDumper($dumper);
        $configuration->dump();

        return $configuration;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function parse($path)
    {
        return Yaml::parse($path);
    }
}
