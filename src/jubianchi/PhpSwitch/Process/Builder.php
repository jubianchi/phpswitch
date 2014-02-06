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
use Symfony\Component\Process\ProcessBuilder;

class Builder extends ProcessBuilder
{
    protected $root = false;
    protected $askpass;
    protected $password;

    public function __construct(array $arguments = array(), AskPass $askpass = null)
    {
        parent::__construct($arguments);

        $this->askpass = $askpass;
    }

    public function setRoot($root = true)
    {
        $this->root = $root;

        return $this;
    }

    public function getProcess($rootProcessFactory = null)
    {
        $process = parent::getProcess();

        if ($this->root) {
            $rootProcessFactory = $rootProcessFactory ?: function($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array()) {
                return new Root($commandline, $cwd, $env, $stdin, $timeout, $options);
            };

            $process = $rootProcessFactory(
                $process->getCommandLine(),
                $process->getWorkingDirectory(),
                $process->getEnv(),
                $process->getStdin(),
                $process->getTimeout(),
                $process->getOptions()
            );

            if(null !== $this->askpass) {
                $process->setPassword($this->askpass->getPassword());
            }
        }

        return $process;
    }
}
