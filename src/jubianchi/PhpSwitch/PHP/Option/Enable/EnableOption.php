<?php
namespace jubianchi\PhpSwitch\PHP\Option\Enable;

use jubianchi\PhpSwitch\PHP\Option\Option;

abstract class EnableOption extends Option
{
    const ARG = null;
    const ALIAS = null;

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS ?: '--enable-' . static::ARG;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getAlias();
    }
}
