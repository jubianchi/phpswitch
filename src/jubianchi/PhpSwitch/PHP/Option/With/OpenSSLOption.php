<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\PHP\Option\Enable;

class OpenSSLOption extends WithOption
{
    const ARG = 'openssl';
    const ALIAS = '--with-openssl';
    const MODE = InputOption::VALUE_OPTIONAL;
}
