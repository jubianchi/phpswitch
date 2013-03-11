<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Version;

interface OptionInterface
{
    public function preInstall(Version $version, InputInterface $input, OutputInterface $output);
    public function postInstall(Version $version, InputInterface $input, OutputInterface $output);
    public function __toString();
}
