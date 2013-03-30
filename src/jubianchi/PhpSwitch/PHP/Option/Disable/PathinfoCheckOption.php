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

class PathinfoCheckOption extends DisableOption
{
    const ARG = 'disable-path-info-check';
    const DESC = 'If this is disabled, paths such as /info.php/test?a=b will fail to work. Available since PHP 4.3.0. For more information see the Apache Manual (http://httpd.apache.org/docs/current/mod/core.html#acceptpathinfo).';
}
