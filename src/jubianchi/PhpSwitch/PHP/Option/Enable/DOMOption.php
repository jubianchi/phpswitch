<?php
namespace jubianchi\PhpSwitch\PHP\Option\Enable;

class DOMOption extends EnableOption
{
    const ARG = 'dom';

    public function requires()
    {
        return array(
            new LibXMLOption()
        );
    }
}
