<?php
namespace jubianchi\PhpSwitch\Console\Command\Config;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\Console\Command\Command;

class InfoCommand extends Command
{
    const NAME = 'config:info';
    const DESC = 'Displays configuration';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->displaySection($output, $this->getConfiguration());

        $output->write(PHP_EOL);

        foreach ($this->getApplication()->getContainer()->keys() as $key) {
            $value = $this->getApplication()->getContainer()->offsetGet($key);

            if (is_scalar($value)) {
                $output->writeln(sprintf('<info>%-35s</info><comment>%s</comment>', $key, $value));
            } else {
                $output->writeln(sprintf('<info>%-35s</info><comment>%s</comment>', $key, get_class($value)));
            }
        }

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

        foreach ($section as $key => $value) {
            if (false === is_array($value)) {
                if (true === is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                $output->writeln(sprintf($margin . '<info>%-15s</info> <comment>%s</comment>', $key, $value));
            } else {
                $output->writeln(sprintf($margin . '<info>%s</info>', $key));
                $this->displaySection($output, new \ArrayIterator($value), $level + 1);
            }
        }
    }
}
