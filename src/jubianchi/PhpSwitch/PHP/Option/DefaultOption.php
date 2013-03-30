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

class DefaultOption extends AliasOption
{
    const ARG = 'default';
    const DESC = 'A default set of configure options';

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array(
            new DisableAllOption(),
            new Enable\CTypeOption(),
            new Enable\DOMOption(),
            new Enable\JSONOption(),
            new Enable\PHAROption(),
            new Enable\SimpleXMLOption(),
            new Enable\XMLOption(),
            new Enable\TokenizerOption(),
            new With\XSLOption()
        );
    }
}
