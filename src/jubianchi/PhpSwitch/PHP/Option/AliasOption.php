<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\PHP\Option\Enable;

abstract class AliasOption extends Option
{
    const ARG = 'atoum';
    const DESC = 'atoum configure options';

    /**
     * @return string
     */
    public function getDesc()
    {
        return parent::getDesc() . sprintf('<comment>(%s)</comment>', $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->requires());
    }
}
