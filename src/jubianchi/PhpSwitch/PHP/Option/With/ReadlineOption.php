<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\PHP\Version;

class ReadlineOption extends WithOption
{
    const ARG = 'readline';
    const ALIAS = '--with-readline';
    const MODE = InputOption::VALUE_OPTIONAL;
}
