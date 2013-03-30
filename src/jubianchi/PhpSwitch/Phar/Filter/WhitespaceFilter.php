<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Phar\Filter;

use jubianchi\PhpSwitch\Phar;

class WhitespaceFilter implements Phar\Filter
{
    public function __invoke($contents, array $tokens)
    {
        $contents = preg_replace('/[ \t]+/', ' ', $contents);
        $contents = preg_replace('/(?:\r\n|\r|\n)+/', "\n", $contents);
        $contents = preg_replace('/(?:\n ?)+/', "\n", $contents);

        return trim($contents) . "\n";
    }
}
