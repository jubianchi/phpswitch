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

class DisableAllOption extends Option
{
    const ARG = 'disable-all';
    const ALIAS = '--disable-all';
    const DESC = 'Disables all <comment>(--disable-all)</comment>';
}
