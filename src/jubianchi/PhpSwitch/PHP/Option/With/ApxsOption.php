<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Process\Process;

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

            $this->command->getApplication()->getService('app.process.builder')
                ->create(array('cp', $path, $path . '.backup'), true)
                ->getProcess()
                ->run();
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

            $this->command->getApplication()->getService('app.process.builder')
                ->create(array('cp', $path . '.backup', $path), true)
                ->getProcess()
                ->run();

            $this->command->getApplication()->getService('app.process.builder')
                ->create(array('rm', $path . '.backup'), true)
                ->getProcess()
                ->run();
        }
    }

    public function installModule(OutputInterface $output, Version $version)
    {
        $module = implode(
            DIRECTORY_SEPARATOR,
            array(
                $this->command->getApplication()->getService('parameters')->offsetGet('app.workspace.sources.path'),
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

            $this->command->getApplication()->getService('app.process.builder')
                ->create(array('cp', $module , $out), true)
                ->getProcess()
                ->run();

            $this->command->getApplication()->getService('app.process.builder')
                ->create(array('chmod', '755' , $out), true)
                ->getProcess()
                ->run();
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
