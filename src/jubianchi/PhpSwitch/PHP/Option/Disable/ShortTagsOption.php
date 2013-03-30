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

class ShortTagsOption extends DisableOption
{
    const ARG = 'disable-short-tags';
    const DESC = 'Disable the short-form <?php start tag by default.';
}
