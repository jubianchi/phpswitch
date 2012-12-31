<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use jubianchi\PhpSwitch\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class Option
{
    const ARG = null;
    const ALIAS = null;
    const DESC = null;

    /** @var \jubianchi\PhpSwitch\Console\Command\Command */
    protected $command;

    /**
     * @param \jubianchi\PhpSwitch\Console\Command\Command $command
     *
     * @return Option
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
        $this->applyArgument($command);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::ARG;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return static::DESC ?: 'Enables ' . static::ARG . sprintf($this->getAlias() ? ' <comment>(%s)</comment>' : '', $this->getAlias());
    }

    /**
     * @param \jubianchi\PhpSwitch\Console\Command\Command $command
     *
     * @return Option
     */
    public function applyArgument(Command $command)
    {
        if (static::ARG !== null && false === $command->getDefinition()->hasArgument(static::ARG)) {
            $command->addOption(
                static::ARG,
                null,
                InputOption::VALUE_NONE,
                $this->getDesc()
            );
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return bool
     */
    public function isEnabled(InputInterface $input)
    {
        return (false !== $input->getOption($this->getName()));
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS ?: '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getAlias();
    }
}
