<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        return static::ALIAS ?: ('--enable-' . static::ARG);
    }
}
