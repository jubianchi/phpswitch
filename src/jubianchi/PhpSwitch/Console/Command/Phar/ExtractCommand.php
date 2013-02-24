<?php
namespace jubianchi\PhpSwitch\Console\Command\Phar;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use jubianchi\PhpSwitch\Console\Command\Command;

class ExtractCommand extends Command
{
    const NAME = 'phar:extract';
    const DESC = 'Builds phpswitch Phar';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Phar filename', 'phpswitch.phar')
            ->addArgument('output', InputArgument::OPTIONAL, 'Output directory', 'phar')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $input->getArgument('name');
        $output = $input->getArgument('output');

        $phar = new \Phar($name);
        $phar->extractTo($output);
    }
}
