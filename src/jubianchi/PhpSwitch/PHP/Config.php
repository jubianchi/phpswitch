<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP;

class Config
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getValue(Version $version, $name)
    {
        $path = $this->getConfigurationFilePath($version, $name);

        if (false === is_file($path)) {
            if (false === ($value = ini_get($name))) {
                throw new \InvalidArgumentException(sprintf('Configuration directive %s is not managed by phpswitch', $name));
            }

            return $value;
        }

        $ini = parse_ini_string(file_get_contents($path));

        return $ini[$name];
    }

    public function setValue(Version $version, $name, $value)
    {
        $path = $this->getConfigurationFilePath($version, $name);

        if (false === is_file($path)) {
            if (false === is_writable(dirname($path))) {
                throw new \RuntimeException('You don\'t have the required permission to edit configuration');
            }
        }

        file_put_contents($path, $name . ' = "' . $value . '"' . PHP_EOL);

        return $this;
    }

    protected function getConfigurationFilePath(Version $version, $name)
    {
        $directory = implode(
            DIRECTORY_SEPARATOR,
            array(
                $this->directory,
                $version
            )
        );

        if (false === is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf('PHP version %s is not installed', $version));
        }

        return implode(
            DIRECTORY_SEPARATOR,
            array(
                $directory,
                'var',
                'db',
                $name . '.ini'
            )
        );
    }
}
