<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputOption;

class ReadlineOption extends WithOption
{
    const ARG = 'readline';
    const ALIAS = '--with-readline';
    const MODE = InputOption::VALUE_OPTIONAL;
}
