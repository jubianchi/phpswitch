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

class CommentFilter implements Phar\Filter
{
    public function __invoke($contents, array $tokens)
    {
        $contents = '';
        foreach ($tokens as $token) {
            if (is_array($token) && in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $contents .= "\n";
            } else {
                $contents .= is_array($token) ? $token[1] : $token;
            }
        }

        return $contents;
    }
}
