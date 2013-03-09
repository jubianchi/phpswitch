<?php
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
