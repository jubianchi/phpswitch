<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Application;

use jubianchi\PhpSwitch\Config\Configuration as BaseConfiguration;
use Traversable;

class Configuration implements \IteratorAggregate
{
    /** @var \jubianchi\PhpSwitch\Config\Configuration  */
    protected $local;

    /** @var \jubianchi\PhpSwitch\Config\Configuration  */
    protected $global;

    public function __construct(BaseConfiguration $local, BaseConfiguration $global)
    {
        $this->local = $local;
        $this->global = $global;
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
        try {
            $value = $this->local->get($offset);
        } catch (\InvalidArgumentException $exception) {
            try {
                $value = $this->global->get($offset);
            } catch(\InvalidArgumentException $exception) {
                if(null === $default) {
                    throw $exception;
                }

                $value = $default;
            }
        }

        return $value;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     * @param bool   $local
     *
     * @throws \InvalidArgumentException
     *
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function set($offset, $value, $local = false)
    {
        if (true === $local) {
            $configuration = $this->local->set($offset, $value);
        } else {
            $configuration = $this->global->set($offset, $value);
        }

        $configuration->dump();

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_replace_recursive(
            $this->global->getValues(),
            $this->local->getValues()
        ));
    }

    /**
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function getLocal()
    {
        return $this->local;
    }
}
