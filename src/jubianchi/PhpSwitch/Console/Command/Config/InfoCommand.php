<?php
namespace jubianchi\PhpSwitch\Console\Command\Config;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\Console\Command\Command;

class InfoCommand extends Command
{
    const NAME = 'config:info';
    const DESC = 'Displays configuration';

    const INDENT = '    ';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            array(
                sprintf(
                    '<info>%s</info> version <comment>%s</comment>',
                    $this->getApplication()->getName(),
                    $this->getApplication()->getVersion()
                ),
                sprintf(
                    '<info>Installation directory:</info> <comment>%s</comment>' . PHP_EOL,
                    realpath(__DIR__ . '/../../../../../..')
                )
            )
        );

        $this->displaySection($output, $this->getConfiguration());

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Traversable                                      $section
     * @param int                                               $level
     */
    protected function displaySection(OutputInterface $output, \Traversable $section, $level = 0)
    {
        $margin = str_repeat(self::INDENT, $level);

        foreach($section as $key => $value) {
            if(false === is_array($value)) {
                if(true === is_bool($value))
                {
                    $value = $value ? 'true' : 'false';
                }

                $output->writeln(sprintf($margin . '<info>%-15s</info> <comment>%s</comment>', $key, $value));
            } else {
                $output->writeln(sprintf($margin . '<info>%s</info>', $key));
                $this->displaySection($output, $value, $level + 1);
            }
        }
    }
}
