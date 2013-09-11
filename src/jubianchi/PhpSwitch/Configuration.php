<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Configuration\Dumper;

class Configuration implements \IteratorAggregate
{
    const ROOT = 'phpswitch';

    /** @var string */
    protected $name;

    /** @var string */
    protected $path;

    /** @var array */
    private $configuration = array();

    /** @var \jubianchi\PhpSwitch\Configuration\Dumper */
    private $dumper;

    public function __construct($name = '.phpswitch.yml', Dumper $dumper = null)
    {
        $this->name = $name;
        $this->setDumper($dumper ?: new Dumper());
    }

    /**
     * @param string $offset
     * @param mixed  $default
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function get($offset, $default = null)
    {
        $offset = str_replace('-', '_', $offset);
        $offset = preg_split('/(?<!\\\)\./', $offset);
        $reference = $this->configuration;
        $current = $sep = '';

        foreach ($offset as $key) {
            $key = preg_replace('/\\\\./', '.', $key);
            $current .= $sep . $key;

            if (false === array_key_exists($key, $reference) || null === $reference[$key]) {
                if (null === $default) {
                    throw new \InvalidArgumentException(sprintf('Offset %s does not exist', $current));
                } else {
                    return $default;
                }
            }

            $reference = & $reference[$key];

            $sep = '.';
        }

        return $reference;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws \InvalidArgumentException
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function set($offset, $value)
    {
        $offset = str_replace('-', '_', $offset);
        $offset = preg_split('/(?<!\\\)\./', $offset);
        $reference = & $this->configuration;
        $current = $sep = '';

        foreach ($offset as $key) {
            $key = preg_replace('/\\\\./', '.', $key);
            $current .= $sep . $key;
            if (false === isset($reference[$key])) {
                $reference[$key] = null;
            }

            $reference = & $reference[$key];

            $sep = '.';
        }

        $reference = $value;

        return $this;
    }

    /**
     * @param array $values
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function setValues(array $values)
    {
        $this->configuration = $values;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->configuration;
    }

    /**
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
    return new \RecursiveArrayIterator($this->configuration);
    }

    /**
     * @throws \RuntimeException
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function dump()
    {
        $this->dumper->dump($this->path, $this);

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\Configuration\Dumper $dumper
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function setDumper(Dumper $dumper)
    {
        $this->dumper = $dumper;

        return $this;
    }

    /**
     * @return \jubianchi\PhpSwitch\Configuration\Dumper
     */
    public function getDumper()
    {
        return $this->dumper;
    }

    /**
     * @param string $path
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
