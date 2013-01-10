<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console;
use \Symfony\Component\Console\Input\InputArgument;
use jubianchi\PhpSwitch\Console\Command\Command;

class ConfigCommand extends Command
{
    const NAME = 'php:config';
    const DESC = 'Get or set configuration';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('value', InputArgument::OPTIONAL)
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        parent::execute($input, $output);

        try {
            $version = $this->getConfiguration()->get('version');
            $path = implode(
                DIRECTORY_SEPARATOR,
                array(
                    $this->getApplication()->getService('app.workspace.installed.path'),
                    $version,
                    'var',
                    'db',
                    $input->getArgument('name') . '.ini'
                )
            );

            $value = $input->getArgument('value');
            if (is_file($path)) {
                if (null !== $value) {
                    unlink($path);
                } else {
                    $ini = parse_ini_string(file_get_contents($path));

                    $output->writeln(sprintf(
                        '<info>%s</info> => <comment>%s</comment>',
                        $input->getArgument('name'),
                        $ini[$input->getArgument('name')]
                    ));
                }
            } else {
                if (null === $value) {
                   return 1;
                }
            }

            if ($value) {
               file_put_contents($path, $input->getArgument('name') . ' = "' . $value . '"');
            }
        } catch (\InvalidArgumentException $exception) {
            return $exception->getCode();
        }
    }
}
