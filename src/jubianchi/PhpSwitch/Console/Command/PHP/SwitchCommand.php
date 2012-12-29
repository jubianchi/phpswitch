<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
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

        $this->addArgument('version', InputArgument::REQUIRED, 'Switch PHP version (alias-x.y.z)');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $version = ('off' === $version ? null : $version);

        $this->getConfiguration()
            ->set('version', $version)
            ->dump()
        ;

        $this->log(
            sprintf('PHP switched to <info>%s</info>', $version ?: 'system default version'),
            \Monolog\Logger::INFO,
            $output
        );

        return 0;
    }
}
