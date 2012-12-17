<?php
namespace jubianchi\PhpSwitch\Console\Command;

use jubianchi\PhpSwitch;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\Exception\DirectoryExistsException;

class InitCommand extends Command
{
    const NAME = 'init';
    const DESC = 'Initializes PhpSwitch environment';

    const INDENT = '    ';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directories = array(
            $workspace = $this->getApplication()->getService('app.workspace.path'),
            $this->getApplication()->getService('app.workspace.downloads.path'),
            $this->getApplication()->getService('app.workspace.sources.path'),
            $installed = $this->getApplication()->getService('app.workspace.installed.path')
        );

        $status = 0;
        foreach($directories as $directory) {
            try {
                if ($this->makeDirectory($directory)) {
                    $output->writeln(sprintf('Directory <info>%s</info> was created', $directory));
                } else {
                    $output->writeln(sprintf('Directory <error>%s</error> was not created', $directory));
                    $status = 1;
                }
            } catch (DirectoryExistsException $exc) {
                $output->writeln(sprintf('Directory <info>%s</info> already exists', $directory));
            }
        }

        $path = $this->getApplication()->getService('app.path');
        file_put_contents(
            $workspace . '/.phpswitchrc',
            <<<SHELL
#!/bin/bash

if [ -z \$PHPSWITCH_ORIG_PATH ]
then
    export PHPSWITCH_ORIG_PATH=\$PATH
fi

php() {
    local VERSION

    case "$1" in
        switch)
            if [ "$2" == "off" ]
            then
                export PATH=\$PHPSWITCH_ORIG_PATH
                $path/bin/phpswitch php:switch off
            else
                if [ "$2" != on ]
                then
                    $path/bin/phpswitch php:switch $2
                fi

                VERSION=\$($path/bin/phpswitch php:current)

                if [ $? ] && [ ! -z \$VERSION ]
                then
                    export PATH=$installed/\$VERSION/bin:\$PHPSWITCH_ORIG_PATH
                fi
            fi

            php -v

            return 0
            ;;
    esac

    /usr/bin/env php $*
}

SHELL
        );

        $output->writeln(
            sprintf(
                'You should source <info>%s</info> to use PhpSwitch',
                $this->getApplication()->getService('app.user.path') . '/.phpswitchrc'
            )
        );

        return $status;
    }

    /**
     * @param string $path
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    protected function checkWriteAccess($path)
    {
        $write = is_writable($path);

        if (false === $write) {
            throw new \RuntimeException(sprintf('You don\'t have write access on %s', $path));
        }

        return $write;
    }

    /**
     * @param $path
     *
     * @throws \RuntimeException
     * @throws \jubianchi\PhpSwitch\Exception\DirectoryExistsException
     *
     * @return bool
     */
    protected function makeDirectory($path)
    {
        $this->checkWriteAccess(dirname($path));

        if(false === file_exists($path)) {
            $create = mkdir($path);

            if(false === $create) {
                throw new \RuntimeException(sprintf('Could not create directory %s', $path));
            }
        } else {
            throw new DirectoryExistsException($path);
        }

        return $create;
    }
}
