<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Version;

interface OptionInterface
{
    function preInstall(Version $version, InputInterface $input, OutputInterface $output);
    function postInstall(Version $version, InputInterface $input, OutputInterface $output);
    function __toString();
}
