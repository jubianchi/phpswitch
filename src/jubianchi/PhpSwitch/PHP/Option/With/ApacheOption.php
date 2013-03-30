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

use Symfony\Component\Console\Input\InputOption;

class ApacheOption extends WithOption
{
    const ARG = 'apache';
    const MODE = InputOption::VALUE_REQUIRED;
    const DESC = 'Build a static Apache module. DIR is the top-level Apache build directory, defaults to /usr/local/apache.';
}
