<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Process\Builder;

use jubianchi\PhpSwitch\Process\AskPass;
use jubianchi\PhpSwitch\Process\Builder;

class Factory
{
    protected $askpass;

    public function __construct(AskPass $askpass = null)
    {
        $this->askpass = $askpass;
    }

    public function create(array $arguments = array())
    {
        return new Builder($arguments, $this->askpass);
    }
}
