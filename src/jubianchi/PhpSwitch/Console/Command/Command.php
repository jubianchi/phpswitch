<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Monolog\Logger;

abstract class Command extends BaseCommand
{
    const NAME = 'command';
    const DESC = '';

    const INDENT = '    ';

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name ?: static::NAME);

        $this->setDescription(static::DESC);
    }

    /**
     * @return \jubianchi\PhpSwitch\Console\Application\Configuration
     */
    public function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->getApplication()->getLogger();
    }

    /**
     * @param string[]|string $messages
     * @param int             $level
     *
     * @return \jubianchi\PhpSwitch\Console\Command\Command
     */
    public function log($messages, $level = Logger::INFO)
    {
        if (false === is_array($messages)) {
            $messages = array($messages);
        }

        foreach ($messages as $message) {
            $this->getLogger()->addRecord($level, $message);
        }

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false === is_dir($this->getApplication()->getParameter('app.workspace.path'))) {
            throw new \RuntimeException(
                sprintf(
                    '%s is not initialized. Please run init command',
                    $this->getApplication()->getName()
                )
            );
        }
    }
}
