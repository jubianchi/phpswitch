<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Process;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

class Root extends Process
{
    protected $cleanCommandline;
    protected $password;

    public function __construct($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = null, array $options = array())
    {
        parent::__construct($commandline, $cwd, $env, $stdin, $timeout ?: 60, $options);

        $this->setCommandLine($commandline);
    }

    public function setPassword($password)
    {
        $this->password = $password;

        $this->setCommandLine($this->cleanCommandline);

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setCommandLine($commandline)
    {
        $this->cleanCommandline = $commandline;
        $commandline = 'sudo -S ' . $this->cleanCommandline . '; sudo -k';

        if (null !== $this->password) {
            $commandline = 'echo ' . escapeshellarg($this->password) . ' | ' . $commandline;
        }

        return parent::setCommandLine($commandline);
    }

    /*protected $builder;
    protected $askpass;
    protected $password;*/

    /*public function __construct(Builder $builder)
    {
        $this->commands = array();
        $this->builder = $builder;

        $process = $builder
            ->create(
                array(
                    'sudo',
                    '-n',
                    'whoami'
                )
            )
            ->getProcess();

        $process->run();

        if (false === $process->isSuccessful()) {
            echo "\n\033[0;36mYou will be asked to provide your password\nto process the following actions.\033[0m\n\n";
            $this->askpass = true;
        }
    }*/

    /*
    public function start()
    {
        $dialog = new \Symfony\Component\Console\Helper\DialogHelper();
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();

        $i = 0;
        do {
            $this->password = $dialog->askHiddenResponse($output, 'Enter your password: ');

            $process = $process = $this->builder
                ->create(array('sudo', '-S', 'whoami'))
                ->setInput($this->password . PHP_EOL)
                ->getProcess();
        } while(0 !== $process->run() && ++$i < 3);

        if(false === $process->isSuccessful()) {
            throw new \RuntimeException('Wrong password');
        }

        $output->write(PHP_EOL);

        return $this;
    }

    public function run(array $arguments, $cwd = null, array $env = array(), $callback = null)
    {
        if(true === $this->askpass && null === $this->password) {
            $this->start();
        }

        $process = $process = $this->builder
            ->create($arguments)
            ->setWorkingDirectory($cwd)
            ->setInput($this->password . PHP_EOL)
            ->getProcess()
            ->setEnv(array_replace(
                array('PATH' => getenv('PATH')),
                $env
            ));

        $process->setCommandLine('sudo -S ' . $process->getCommandLine());
        $process->run($callback);

        return $this;
    }
    */
}
