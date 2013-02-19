<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $input->getArgument('name');
        $value = $input->getArgument('value');

        if (null !== $value) {
            $this->setValue($name, $value);
        } else {
            $this->getValue($name);
        }
    }

    public function getValue($name)
    {
        $path = $this->getConfigurationFilePath($name);

        if (false === is_readable($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Configuration directive %s is not managed by %s',
                $name,
                $this->getApplication()->getName()
            ));
        }

        $ini = parse_ini_string(file_get_contents($path));

        return $ini[$name];
    }

    public function setValue($name, $value)
    {
        $path = $this->getConfigurationFilePath($name);

        if (false === is_writable(dirname($path))) {
            throw new \RuntimeException('You don\'t have the required permission to edit configuration');
        }

        file_put_contents($path, $name . ' = "' . $value . '"');
    }

    protected function getConfigurationFilePath($name)
    {
        $version = $this->getConfiguration()->get('version');

        return implode(
            DIRECTORY_SEPARATOR,
            array(
                $this->getApplication()->getService('app.workspace.installed.path'),
                $version,
                'var',
                'db',
                $name . '.ini'
            )
        );
    }
}
