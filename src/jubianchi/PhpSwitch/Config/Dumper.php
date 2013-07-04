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
    public function dump($name, Configuration $configuration, $directory = null)
    {
        $path = $this->directories[$directory ?: self::GLOBAL_DIR] . DIRECTORY_SEPARATOR . $name;
        file_put_contents(
            $path,
            Yaml::dump(array(Configuration::ROOT => $configuration->getValues()), 5, 2)
        );

        return $this;
    }
}
