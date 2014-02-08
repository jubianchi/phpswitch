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

use jubianchi\PhpSwitch\Configuration;

class Collection implements \IteratorAggregate
{
    /** @var \jubianchi\PhpSwitch\Configuration[] */
    protected $configs = array();

    public function add(Configuration $configuration)
    {
        $this->configs[] = $configuration;

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
        $value = null;

        foreach (array_reverse($this->configs) as $configuration) {
            if ($configuration->has($offset)) {
                $value = $configuration->get($offset);

                break;
            }
        }

        if (null === $value && null === $default) {
            throw new \InvalidArgumentException(sprintf('Offset %s does not exist', $offset));
        }

        if (null === $value) {
            $value = $default;
        }

        return $value;
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
        $matched = null;

        foreach (array_reverse($this->configs) as $configuration) {
            if ($configuration->has($offset)) {
                $matched = $configuration;
            }
        }

        if (null === $matched) {
            $matched = current(array_reverse($this->configs));
        }

        $matched->set($offset, $value);

        return $this;
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function has($offset)
    {
        foreach ($this->configs as $configuration) {
            if ($configuration->has($offset)) {
                return true;
            }
        }

        return false;
    }

    public function getIterator()
    {
        $values = array();

        foreach ($this->configs as $configuration) {
            $values = array_replace_recursive($values, iterator_to_array($configuration->getIterator()));
        }

        return new \ArrayIterator($values);
    }
}
