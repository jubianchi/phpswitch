<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console;
use jubianchi\PhpSwitch\Console\Command\Command;

class CurrentCommand extends Command
{
    const NAME = 'php:current';
    const DESC = 'Displays current PHP version';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        try {
            $output->writeln($this->getConfiguration()->get('version'));
        } catch (\InvalidArgumentException $exception) {
            return $exception->getCode();
        }

        return 0;
    }
}
