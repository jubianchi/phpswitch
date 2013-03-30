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

class AtoumOption extends AliasOption
{
    const ARG = 'atoum';
    const DESC = 'atoum configure options';

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array(
            new DisableAllOption(),
            new Enable\CLIOption(),
            new Enable\PHAROption(),
            new Enable\HashOption(),
            new Enable\JSONOption(),
            new Enable\XMLOption(),
            new Enable\SessionOption(),
            new Enable\TokenizerOption(),
            new Enable\PosixOption(),
            new Enable\DOMOption(),
            new Enable\MBStringOption()
        );
    }
}
