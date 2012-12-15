<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console;
use jubianchi\PhpSwitch\PHP\Finder;
use jubianchi\PhpSwitch\Console\Command\Command;

class ListCommand extends Command
{
    const NAME = 'php:list';
    const DESC = 'Lists PHP versions';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $finder = new Finder();
        $builder = $this->getApplication()->getBuilder();

        foreach ($finder as  $version) {
            $dest = $builder->getDestination($version);

            $output->writeln(
                sprintf(
                    '<info>%-15s</info> <comment>%s</comment>',
                    $version->getName() . (is_dir($dest) ? '*' : ''),
                    sprintf($version->getUrl(), 'a')
                )
            );
        }

        return 0;
    }
}
