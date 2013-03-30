<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Process;

use Symfony\Component\Process\ProcessBuilder;

class Builder extends ProcessBuilder
{
    public function get(array $arguments = array())
    {
        return new static($arguments);
    }
}
