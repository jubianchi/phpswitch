<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Finder\Finder;
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
            ->addOption('apache2', 'a', InputOption::VALUE_NONE)
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $version = ('off' === $version ? null : $version);

        if (null !== $version) {
            $path = $this->getApplication()->getService('app.workspace.installed.path');
            $finder = new Finder();
            $finder
                ->in($path)
                ->directories()
                ->name('*-*')
                ->depth(0)
            ;

            if (0 === count($finder)) {
                throw new \InvalidArgumentException(sprintf('Version %s is not installed', $version));
            }
        } else {
            $result = $status = null;
            exec('command -v apxs', $result);
            exec($result[0] . ' -q LIBEXECDIR', $result, $status);

            if (0 === $status && is_writable($result[1])) {
                $original = $result[1] . DIRECTORY_SEPARATOR . 'libphp5.so';
                $backup = $result[1] . DIRECTORY_SEPARATOR . 'libphp5.so.system';

                if(is_file($backup)) {
                    if (is_file($original)) {
                       unlink($original);
                    }

                    $output->writeln('Restoring <info>system default</info> Apache2 module');
                    copy($backup, $original);
                    chmod($original, 0755);
                }
            }
        }

        if ($input->getOption('apache2')) {
            $result = $status = null;
            exec('command -v apxs', $result);
            exec($result[0] . ' -q LIBEXECDIR', $result, $status);

            if (0 === $status && is_writable($result[1])) {
                $original = $result[1] . DIRECTORY_SEPARATOR . 'libphp5.so';
                $module = $result[1] . DIRECTORY_SEPARATOR . 'libphp5-' . $version . '.so';

                if(is_file($module)) {
                    if(is_file($original)) {
                        if(false === is_file($original . '.system')) {
                            $output->writeln('Backuping system default Apache2 module');
                            copy($original, $original . '.system');
                        }

                        unlink($original);
                    }

                    $output->writeln(sprintf('Switching Apache2 module to <info>%s</info>', $version));
                    copy($module, $original);
                    chmod($original, 0755);
                }
            }
        }

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
