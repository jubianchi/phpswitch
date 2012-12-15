<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use jubianchi\PhpSwitch\PHP\Finder;
use jubianchi\PhpSwitch\Console\Command\Command;

class SwitchCommand extends Command
{
    const NAME = 'php:switch';
    const DESC = 'Switch PHP version';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this->addArgument('version', InputArgument::REQUIRED, 'Switch PHP version (x.y.z)');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->getConfiguration()
            ->set('version', $input->getArgument('version'))
            ->dump()
        ;

        $output->writeln(sprintf('PHP switched to <info>%s</info>', $input->getArgument('version')));

        return 0;
    }
}
