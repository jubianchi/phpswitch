<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputOption;

class OpenSSLOption extends WithOption
{
    const ARG = 'openssl';
    const ALIAS = '--with-openssl';
    const MODE = InputOption::VALUE_OPTIONAL;
}
