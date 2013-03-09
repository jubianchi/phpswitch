<?php
namespace jubianchi\PhpSwitch\PHP\Option;

class Symfony2Option extends DefaultOption
{
    const ARG = 'symfony2';
    const DESC = 'symfony2 configure options';

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array_merge(
            parent::requires(),
            array(
                new Enable\SessionOption(),
                new With\PCREOption(),
                new Enable\IntlOption(),
                new With\PDOMySQLOption()
            )
        );
    }
}
