<?php
namespace jubianchi\PhpSwitch\Phar\Filter;

use jubianchi\PhpSwitch\Phar;

class FilterCollection implements \Iterator, \ArrayAccess
{
    protected $filters = array();

    public function add(Phar\Filter $filter, $offset = null)
    {
        if (false === in_array($filter, $this->filters, true)) {
            $offset = $offset ?: count($this->filters);

            $this->filters[$offset] = $filter;
        }

        return $this;
    }

    public function apply($contents)
    {
        if (!function_exists('token_get_all')) {
            return $contents;
        }

        if (0 === sizeof($tokens = @token_get_all($contents))) {
            return $contents;
        }

        foreach ($this->filters as $filter) {
            $contents = call_user_func_array($filter, array($contents, $tokens));
        }

        return $contents;
    }

    public function current()
    {
        return current($this->filters);
    }

    public function next()
    {
        next($this->filters);
    }

    public function key()
    {
        return key($this->filters);
    }

    public function valid()
    {
        return $this->key() !== null;
    }

    public function rewind()
    {
        reset($this->filters);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->filters);
    }

    public function offsetGet($offset)
    {
        return $this->filters[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->add($value, $offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->filters[$offset]);
    }
}
