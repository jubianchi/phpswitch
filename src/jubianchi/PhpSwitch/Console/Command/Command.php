<?php
namespace jubianchi\PhpSwitch\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;

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
     * @return \jubianchi\PhpSwitch\Config\Configuration
     */
    public function getConfiguration()
    {
        return $this->getApplication()->getConfiguration();
    }

    /**
     * @return \jubianchi\PhpSwitch\Console\Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}
