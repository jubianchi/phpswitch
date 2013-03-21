<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use jubianchi\PhpSwitch\PHP\Option\Option;

abstract class WithOption extends Option
{
	/**
	 * @return string
	 */
	public function getAlias()
	{
		return static::ALIAS ?: ('--with-' . static::ARG);
	}
}
