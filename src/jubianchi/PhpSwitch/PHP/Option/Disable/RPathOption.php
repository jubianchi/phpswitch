<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option\Disable;

class RPathOption extends DisableOption
{
    const ARG = 'disable-rpath';
    const DESC = 'Disable passing additional runtime library search paths.';
}
