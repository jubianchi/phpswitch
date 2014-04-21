<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Command\PHP;

use jubianchi\PhpSwitch\PHP\Option\With;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this
            ->addArgument('version', InputArgument::REQUIRED, 'Switch PHP version (alias-x.y.z)')
            ->addOption('save', 's', InputOption::VALUE_NONE)
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

        $configuration = $this->getHelper('configuration');
        $oldVersion = $configuration->getCurrentVersion();
        $version = $argument = $input->getArgument('version');

        if (null !== $version) {
            if ('off' === $version) {
                $version = null;
            } else {
                try {
                    $version = Version::fromString($version === 'on'
                        ? $input->getOption('save') ?  $configuration->getCurrentLocalVersion() : $configuration->getCurrentGlobalVersion()
                        : $version
                    );
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
            }
        }

        if (null === $version) {
            if($oldVersion !== null && $oldVersion !== (string) $version) {
                $this->restoreSystemModule($output, Version::fromString($oldVersion));
            }
        } else {
            if($oldVersion !== (string) $version) {
                $this->switchModule($output, $version);
            }

            if ($argument !== 'on' && $argument !== 'off') {
                if ($input->getOption('save')) {
                    $configuration->setVersionLocally($version);
                } else {
                    $configuration->setVersionGlobally($version);
                }
            }
        }

        if ($argument !== 'off') {
            if ($input->getOption('save')) {
                $configuration->enableLocally();
            } else {
                $configuration->enableGlobally();
            }

            $output->writeln(sprintf(($input->getOption('save') ? 'Local' : 'Global') . ' PHP switched to <info>%s</info>', $version ?: 'system default version'));
        } else {
            if ($input->getOption('save')) {
                $configuration->disableLocally();
            } else {
                $configuration->disableGlobally();
            }
            $output->writeln(sprintf(($input->getOption('save') ? 'Local' : 'Global') . ' PHP switched <info>off</info>'));
        }

        return 0;
    }

    public function backupSystemModule(OutputInterface $output, Version $version)
    {
        $config = $this->getConfiguration()->get('versions.' . str_replace('.', '-', $version) . '.options', '');
        /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options */
        $options = $this->getApplication()->getService('app.php.option.normalizer')->denormalize($config);

        $apache = $options->contains(With\ApacheOption::ARG)
            || $options->contains(With\ApxsOption::ARG)
            || $options->contains(With\Apxs2Option::ARG);

        if(true === $apache) {
            $path = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';

            if (is_file($path)) {
                $backup = $path . '.system';

                if (false === is_file($backup)) {
                    $output->writeln(array(
                        PHP_EOL . 'Backuping <info>system default</info> Apache2 module',
                        sprintf('    <comment>From: %s</comment>', $path),
                        sprintf('    <comment>To: %s</comment>', $backup)
                    ));

                    $this->getApplication()->getService('app.process.builder')
                        ->create(array('cp', $path, $backup))
                        ->setRoot()
                        ->getProcess()
                        ->run();
                }
            }
        }
    }

    public function restoreSystemModule(OutputInterface $output, Version $version)
    {
        $config = $this->getConfiguration()->get('versions.' . str_replace('.', '-', $version) . '.options', '');
        /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options */
        $options = $this->getApplication()->getService('app.php.option.normalizer')->denormalize($config);

        $apache = $options->contains(With\ApacheOption::ARG)
            || $options->contains(With\ApxsOption::ARG)
            || $options->contains(With\Apxs2Option::ARG);

        if(true === $apache) {
            $original = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';
            $backup = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so.system';

            if (is_file($backup)) {
                if (is_file($original)) {
                    $this->getApplication()->getService('app.process.builder')
                        ->create(array('rm', $original))
                        ->setRoot()
                        ->getProcess()
                        ->run();
                }

                $output->writeln('Restoring <info>system default</info> Apache2 module');
                $this->getApplication()->getService('app.process.builder')
                    ->create(array('mv', $backup, $original))
                    ->setRoot()
                    ->getProcess()
                    ->run();
                $this->getApplication()->getService('app.process.builder')
                    ->create(array('chmod', '755' , $original))
                    ->setRoot()
                    ->getProcess()
                    ->run();

                $this->promptApacheRestart($output);
            }
        }
    }

    public function switchModule(OutputInterface $output, Version $version)
    {
        $config = $this->getConfiguration()->get('versions.' . str_replace('.', '-', $version) . '.options', '');
        /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options */
        $options = $this->getApplication()->getService('app.php.option.normalizer')->denormalize($config);

        $apache = $options->contains(With\ApacheOption::ARG)
            || $options->contains(With\ApxsOption::ARG)
            || $options->contains(With\Apxs2Option::ARG);

        if(true === $apache) {
            $original = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';
            $module = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5-' . $version . '.so';

            if (is_file($module)) {
                if (is_file($original)) {
                    $this->backupSystemModule($output, $version);

                    $this->getApplication()->getService('app.process.builder')
                        ->create(array('rm' , $original))
                        ->setRoot()
                        ->getProcess()
                        ->run();
                }

                $output->writeln(sprintf('Switching Apache2 module to <info>%s</info>', $version));
                $this->getApplication()->getService('app.process.builder')
                    ->create(array('cp', $module, $original))
                    ->setRoot()
                    ->getProcess()
                    ->run();
                $this->getApplication()->getService('app.process.builder')
                    ->create(array('chmod', '755' , $original))
                    ->setRoot()
                    ->getProcess()
                    ->run();

                $this->promptApacheRestart($output);
            }
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
