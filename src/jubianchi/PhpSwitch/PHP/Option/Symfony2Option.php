<?php
namespace jubianchi\PhpSwitch\PHP\Option;

class Symfony2Option extends AliasOption
{
    const ARG = 'symfony2';
    const DESC = 'symfony2 configure options';

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array(
            new DisableAllOption(),
            new Enable\JSONOption(),
            new Enable\SessionOption(),
            new Enable\CTypeOption(),
            new Enable\TokenizerOption(),
            new Enable\XMLOption(),
            new Enable\SimpleXMLOption(),
            new With\PCREOption(),
            new Enable\IntlOption(),
            new With\PDOMySQLOption()
        );
    }
}
