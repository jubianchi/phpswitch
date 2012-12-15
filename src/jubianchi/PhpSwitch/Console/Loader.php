<?php
namespace jubianchi\PhpSwitch\Console;

use jubianchi\PhpSwitch\Console\Command\Finder;

class Loader
{
    /** @var \jubianchi\PhpSwitch\Console\Command\Finder */
    private $finder;

    /**
     * @static
     *
     * @param \jubianchi\PhpSwitch\Console\Command\Finder $commands
     *
     * @return \jubianchi\PhpSwitch\Console\Loader
     */
    public static function get(Finder $commands)
    {
        return new static($commands);
    }

    /**
     * @param \jubianchi\PhpSwitch\Console\Command\Finder $commands
     */
    public function __construct(Finder $commands)
    {
        $this->finder = $commands;
    }

    /**
     * @param \jubianchi\PhpSwitch\Console\Application $application
     *
     * @return \jubianchi\PhpSwitch\Console\Application
     */
    public function load(Application $application)
    {
        foreach($this->finder as $command) {
            $application->add(new $command());
        }

        return $application;
    }
}
