<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;

class ApxsOption extends WithOption
{
    const ARG = 'apxs';
    const MODE = InputOption::VALUE_REQUIRED;
    const DESC = 'Build shared Apache module. FILE is the optional pathname to the Apache apxs tool; defaults to apxs. Make sure you specify the version of apxs that is actually installed on your system and NOT the one that is in the apache source tarball.';

    public function preInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        if (null !== $this->value) {
            $this->backupModule($output);
        }
    }

    public function postInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        if (null !== $this->value) {
            $this->restoreModule($output);

            $this->installModule($output, $version);
        }
    }

    public function backupModule(OutputInterface $output)
    {
        $path = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';

        if (is_file($path)) {
            $output->writeln(array(
                PHP_EOL . 'Backuping <info>current</info> Apache2 module',
                sprintf('    <comment>From: %s</comment>', $path),
                sprintf('    <comment>To: %s.backup</comment>', $path)
            ));

            copy($path, $path . '.backup');
        }
    }

    public function restoreModule(OutputInterface $output)
    {
        $path = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5.so';

        if (is_file($path)) {
            $output->writeln(array(
                PHP_EOL . 'Restoring <info>previous</info> Apache2 module',
                sprintf('    <comment>From: %s.backup</comment>', $path),
                sprintf('    <comment>To: %s</comment>', $path)
            ));

            copy($path . '.backup', $path);
            unlink($path . '.backup');
        }
    }

    public function installModule(OutputInterface $output, Version $version)
    {
        $module = implode(
            DIRECTORY_SEPARATOR,
            array(
                $this->command->getApplication()->getService('app.workspace.sources.path'),
                $version,
                'libs',
                'libphp5.so'
            )
        );

        if (is_file($module)) {
            $out = $this->getLibDir() . DIRECTORY_SEPARATOR . 'libphp5-' . $version . '.so';

            $output->writeln(array(
                PHP_EOL . 'Installing Apache2 module',
                sprintf('    <comment>From: %s</comment>', $module),
                sprintf('    <comment>To: %s</comment>', $out)
            ));

            copy($module, $out);
            chmod($out, 0755);
        }
    }

    protected function getLibDir()
    {
        static $directory;

        if (null === $directory) {
            exec($this->value . ' -q LIBEXECDIR', $result, $status);

            if (0 !== $status) {
                throw new \RuntimeException('Could not find Apache2 modules directory');
            }

            $directory = $result[0];
        }

        return $directory;
    }
}
