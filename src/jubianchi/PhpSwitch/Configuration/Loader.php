<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Configuration;

use Symfony\Component\Yaml\Yaml;
use jubianchi\PhpSwitch\Configuration;

class Loader
{
    /** @var string */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string                                       $directory
     * @param \jubianchi\PhpSwitch\Configuration\Validator $validator
     * @param bool                                         $bubble
     * @param array                                        $exclude
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function load($directory, Validator $validator, $bubble = false, array $exclude = array())
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
        $values = $validator->validate($values);
        $configuration->setPath(end($configs));
        $configuration->setValues($values);

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
