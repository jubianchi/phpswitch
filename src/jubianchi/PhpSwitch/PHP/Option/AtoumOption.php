<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use jubianchi\PhpSwitch\PHP\Option\Enable;

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
