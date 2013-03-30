<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option\With;

class Apxs2Option extends ApxsOption
{
    const ARG = 'apxs2';
    const DESC = 'Build shared Apache 2.0 module. FILE is the optional pathname to the Apache apxs tool; defaults to apxs.';
}
