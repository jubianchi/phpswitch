<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\Config\Dumper;

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

        $this
            ->addArgument('version', InputArgument::REQUIRED, 'Switch PHP version (alias-x.y.z)')
            ->addOption('local', 'l', InputOption::VALUE_NONE, 'Switch PHP version locally')
            ->addOption('global', 'g', InputOption::VALUE_NONE, 'Switch PHP version globaly')
            ->addOption('apache2', 'a', InputOption::VALUE_NONE)
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $version = $input->getArgument('version');
        $version = ('off' === $version ? null : $version);

        if (null !== $version) {
			try {
				$version = Version::fromString($version === 'on' ? $this->getConfiguration()->get('version') : $version);
			} catch(\InvalidArgumentException $exception) {
				return 0;
			}

            if (null !== $version && false === $this->getApplication()->getService('app.php.installer')->isInstalled($version)) {
				$confirm = false;

				if($input->isInteractive()) {
					$confirm = $this->getHelper('dialog')->askConfirmation($output, sprintf('PHP version <info>%s</info> is not installed. Do you want to install it ? ', $version));
				}

				if (false === $confirm) {
					throw new \InvalidArgumentException(sprintf('Version %s is not installed', $version));
				}

				$args = new \Symfony\Component\Console\Input\ArrayInput(array(
					'command' => InstallCommand::NAME,
					'version' => $version->getVersion(),
					'--config' => (string) $version,
					'--alias' => $version->getName()
				));
				$install = new InstallCommand();
				$install->setApplication($this->getApplication());
				$install->run($args, $output);
            }
        } else {
            $this->restoreSystemModule($output);
        }

        if ($input->getOption('apache2')) {
            if (false === is_writable($this->getLibDir())) {
                throw new \RuntimeException(sprintf('%s is not writable', $this->getLibDir()));
            }

            $this->switchModule($output, $version);
        }

        $this->getConfiguration()
            ->set('version', (string) $version)
            ->dump($input->getOption('local') ? Dumper::LOCAL_DIR : Dumper::GLOBAL_DIR)
        ;

        $output->writeln(sprintf('PHP switched to <info>%s</info>', $version ?: 'system default version'));

        return 0;
    }

    public function backupSystemModule(OutputInterface $output)
    {
        $path = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';

        if (is_file($path)) {
            $backup = $path . '.system';

            if (false === is_file($backup)) {
                $output->writeln(array(
                    PHP_EOL . 'Backuping <info>system default</info> Apache2 module',
                    sprintf('    <comment>From: %s</comment>', $path),
                    sprintf('    <comment>To: %s</comment>', $backup)
                ));

                copy($path, $backup);
            }
        }
    }

    public function restoreSystemModule(OutputInterface $output)
    {
        $original = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';
        $backup = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so.system';

        if (is_file($backup)) {
            if (is_file($original)) {
                unlink($original);
            }

            $output->writeln('Restoring <info>system default</info> Apache2 module');
            copy($backup, $original);
            chmod($original, 0755);

            $this->promptApacheRestart($output);
        }
    }

    public function switchModule(OutputInterface $output, $version)
    {
        $original = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';
        $module = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5-' . $version . '.so';

        if (is_file($module)) {
            if (is_file($original)) {
                $this->backupSystemModule($output);

                unlink($original);
            }

            $output->writeln(sprintf('Switching Apache2 module to <info>%s</info>', $version));
            copy($module, $original);
            chmod($original, 0755);

            $this->promptApacheRestart($output);
        }
    }

    protected function getLibDir()
    {
        static $directory;

        if (null === $directory) {
            $result = $status = null;
            exec($this->getApxsPath() . ' -q LIBEXECDIR', $result, $status);

            if (0 !== $status) {
                throw new \RuntimeException('Could not find Apache2 modules directory');
            }

            $directory = $result[0];
        }

        return $directory;
    }

    protected function getApxsPath()
    {
        static $command;

        if (null === $command) {
            $result = $status = null;
            exec('command -v apxs', $result, $status);

            if (0 !== $status) {
                exec('command -v apxs2', $result, $status);

                if (0 !== $status) {
                    throw new \RuntimeException('Could not find apxs command');
                }
            }

            $command = $result[0];
        }

        return $command;
    }

    protected function promptApacheRestart(OutputInterface $output)
    {
        $output->writeln(array(
            'You should <info>restart apache2</info> using one of:',
            '    <comment>- sudo /etc/init.d/apache2 restart</comment>',
            '    <comment>- sudo service apache2 restart</comment>',
            '    <comment>- sudo apachectl restart</comment>',
            '    <comment>- ...</comment>',
        ));
    }

    public function getSynopsis()
    {
        $synopsis = parent::getSynopsis();
        $parts = explode(' ', $synopsis, 2);
        $synopsis .= PHP_EOL . trim(sprintf('%s %s', 'php switch', $parts[1]));

        return $synopsis;
    }
}
