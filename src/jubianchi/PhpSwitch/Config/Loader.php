<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Config;

use Symfony\Component\Yaml\Yaml;

class Loader
{
    /** @var \jubianchi\PhpSwitch\Config\Validator */
    private $validator;

    /** @var string */
    protected $name;

    /**
     * @param string                                $name
     * @param \jubianchi\PhpSwitch\Config\Validator $validator
     */
    public function __construct($name, Validator $validator)
    {
        $this->name = $name;
        $this->validator = $validator;
    }

    /**
     * @param string                             $directory
     * @param \jubianchi\PhpSwitch\Config\Dumper $dumper
     * @param bool                               $bubble
     * @param array                              $exclude
     *
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function load($directory, Dumper $dumper, $bubble = false, array $exclude = array())
    {
        $values = array();
        if (false === $bubble) {
            $configs = array($directory . DIRECTORY_SEPARATOR . $this->name);
        } else {
            preg_match('/^(?:(?P<protocol>[a-z]+\:\/\/))?(?P<path>.*)$/', $directory, $matches);

            $parts = explode(DIRECTORY_SEPARATOR, $matches['path']);
            $basedir = isset($matches['protocol']) ? $matches['protocol'] : '';
            $configs = array();
            foreach ($parts as $part) {
                $dirpath = $basedir . $part;
                if(false === in_array($dirpath, $exclude) && is_readable($dirpath . DIRECTORY_SEPARATOR . $this->name)) {
                    $configs[] = $dirpath . DIRECTORY_SEPARATOR . $this->name;
                }

                $basedir = $dirpath . DIRECTORY_SEPARATOR;
            }
        }

        foreach ($configs as $path) {
            if (is_file($path)) {
                $config = $this->parse($path) ?: array();

                $values = array_replace_recursive($values, $config);
            }
        }

        $configuration = new Configuration();
        $values = $this->validator->validate($values);
        $configuration->setPath(end($configs));
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
