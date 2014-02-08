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

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

class AskPass
{
    protected $builder;
    protected $password;
    protected $output;
    protected $dialog;

    public function __construct(Builder\Factory $builder, OutputInterface $output, DialogHelper $dialog)
    {
        $this->commands = array();
        $this->builder = $builder;
        $this->output = $output;
        $this->dialog = $dialog;
    }

    public function getPassword()
    {
        $process = $this->builder->create(array('sudo', '-n', 'whoami'))->getProcess();
        $process->run();
        $passwordRequired = (false === $process->isSuccessful() || '' !== $process->getErrorOutput());

        if(true === $passwordRequired && null === $this->password)
        {
            $this->output->writeln(array(
                PHP_EOL,
                "\033[0;36mYou will be asked to provide your password\nto process the following actions.\033[0m\n"
            ));

            $i = 0;
            do {
                $this->password = $this->dialog->askHiddenResponse($this->output, "\033[0;36mEnter your password: \033[0m");

                $process = $process = $this->builder
                    ->create(array('sudo', '-S', 'whoami'))
                    ->setInput($this->password . PHP_EOL)
                    ->getProcess();
            } while(0 !== $process->run() && ++$i < 3);

            if(false === $process->isSuccessful()) {
                throw new \RuntimeException('Wrong password');
            }

            $this->output->write(PHP_EOL);
        }

        return $this->password;
    }
}
