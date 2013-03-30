<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

interface OptionInterface
{
    public function preInstall(Version $version, InputInterface $input, OutputInterface $output);
    public function postInstall(Version $version, InputInterface $input, OutputInterface $output);
	public function setCommand(Command $command);
    public function __toString();
}
