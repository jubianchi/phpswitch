<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option;

abstract class AliasOption extends Option
{
    const ARG = 'atoum';
    const DESC = 'atoum configure options';

    /**
     * @return string
     */
    public function getDesc()
    {
        return parent::getDesc() . sprintf('<comment>(%s)</comment>', $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->requires());
    }
}
