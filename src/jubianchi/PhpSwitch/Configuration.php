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

abstract class Configuration implements \IteratorAggregate
{
    const ROOT = 'phpswitch';

    /** @var string */
    protected $path;

    /** @var \jubianchi\PhpSwitch\Configuration\Dumper */
    protected $dumper;

    /** @var \jubianchi\PhpSwitch\Configuration\Validator */
    protected $validator;

    /** @var array */
    protected $configuration = array();

    /**
     * @param string                  $path
     * @param Configuration\Validator $validator
     * @param Configuration\Dumper    $dumper
     */
    public function __construct($path, Configuration\Dumper $dumper = null, Configuration\Validator $validator = null)
    {
        $this->path = $path;
        $this
            ->setDumper($dumper)
            ->setValidator($validator)
            ->doRead()
        ;

    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param \jubianchi\PhpSwitch\Configuration\Dumper $dumper
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function setDumper(Configuration\Dumper $dumper = null)
    {
        $this->dumper = $dumper ?: new Configuration\Dumper();

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
     * @param \jubianchi\PhpSwitch\Configuration\Validator $validator
     *
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function setValidator(Configuration\Validator $validator = null)
    {
        $this->validator = $validator ?: new Configuration\Validator\Pass();

        return $this;
    }

    /**
     * @return \jubianchi\PhpSwitch\Configuration\Dumper
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return string
     */
    abstract public function __toString();

    /**
     * @return array
     */
    abstract public function read();

    protected function doRead()
    {
        $this->configuration = $this->validator->validate($this->read());

        return $this;
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
        $offset = self::parseOffest($offset);
        $reference = & $this->configuration;
        $current = $sep = '';

        foreach ($offset as $key) {
            $key = self::unescapeKey($key);
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
     *
     * @return bool
     */
    public function has($offset)
    {
        $offset = self::parseOffest($offset);
        $reference = & $this->configuration;

        foreach ($offset as $key) {
            $key = self::unescapeKey($key);

            if (false === array_key_exists($key, $reference) || null === $reference[$key]) {
                return false;
            }

            $reference = & $reference[$key];
        }

        return true;
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
        $offset = self::parseOffest($offset);
        $reference = & $this->configuration;
        $current = $sep = '';

        foreach ($offset as $key) {
            $key = self::unescapeKey($key);
            $current .= $sep . $key;
            if (false === isset($reference[$key])) {
                $reference[$key] = null;
            }

            $reference = & $reference[$key];

            $sep = '.';
        }

        $reference = $value;

        $this->validator->validate(array(self::ROOT => $this->configuration));

        return $this;
    }

    /**
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator($this->configuration);
    }

    /**
     * @param string $offset
     *
     * @return array
     */
    private static function parseOffest($offset)
    {
        $offset = str_replace('-', '_', $offset);

        return preg_split('/(?<!\\\)\./', $offset);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function unescapeKey($key)
    {
        return preg_replace('/\\\\./', '.', $key);
    }
}
