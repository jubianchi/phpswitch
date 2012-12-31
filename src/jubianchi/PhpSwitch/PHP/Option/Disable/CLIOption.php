<?php
namespace jubianchi\PhpSwitch\PHP\Option\Disable;

class CLIOption extends DisableOption
{
    const ARG = 'disable-cli';
    const DESC = 'Available with PHP 4.3.0. Disable building the CLI version of PHP (this forces --without-pear). More information is available in the section about Using PHP from the command line (http://php.net/manual/en/features.commandline.php).';
}
