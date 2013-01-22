<?php
namespace jubianchi\PhpSwitch\Config;

use Symfony\Component\Yaml\Yaml;

class Loader
{
    const DIRECTORY_LOCAL = 0;
    const DIRECTORY_BUBBLE = 1;

    /** @var string */
    private $directories;

    /** @var \jubianchi\PhpSwitch\Config\Validator */
    private $validator;

    /**
     * @param array                                 $directories
     * @param \jubianchi\PhpSwitch\Config\Validator $validator
     */
    public function __construct(array $directories, Validator $validator)
    {
        $this->directories = $directories;
        $this->validator = $validator;
    }

    /**
     * @param array                                     $directories
     * @param \jubianchi\PhpSwitch\Config\Configuration $configuration
     * @param \jubianchi\PhpSwitch\Config\Dumper        $dumper
     *
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function load($name, Configuration $configuration, Dumper $dumper)
    {
        $values = array();
        foreach ($this->directories as $directory => $type) {
            if (is_numeric($directory)) {
                $directory = $type;
                $type = self::DIRECTORY_LOCAL;
            }

            if ($type === self::DIRECTORY_LOCAL) {
                $directory = array($directory);
            } else {
                preg_match('/^(?:(?P<protocol>[a-z]+\:\/\/))?(?P<path>.*)$/', $directory, $matches);

                $parts = explode(DIRECTORY_SEPARATOR, $matches['path']);
                $basedir = isset($matches['protocol']) ? $matches['protocol'] : '';
                $directory = array();
                foreach($parts as $part) {
                    $directory[] = $basedir . $part;

                    $basedir = $basedir . $part . DIRECTORY_SEPARATOR;
                }
                unset($part);
            }

            foreach($directory as $dir) {
                $path = $dir . DIRECTORY_SEPARATOR . $name;

                if (is_file($path)) {
                    $config = $this->parse($path) ?: array();

                    $values = array_replace_recursive($values, $config);
                }
            }
        }

        $values = $this->validator->validate($values);
        $configuration->setValues($values);
        $configuration->setDumper($dumper);

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
