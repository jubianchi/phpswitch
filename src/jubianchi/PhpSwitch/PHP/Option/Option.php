<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use jubianchi\PhpSwitch\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class Option
{
    const ARG = null;
    const ALIAS = null;
    const DESC = null;
    const MODE = InputOption::VALUE_NONE;

    /** @var \jubianchi\PhpSwitch\Console\Command\Command */
    protected $command;

    /** @var string */
    protected $value;

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
        return (static::DESC ?: 'Enables ' . static::ARG) . sprintf($this->getAlias() ? ' <comment>(%s)</comment>' : '', $this->getAlias());
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
                static::MODE,
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
        $value = $input->getOption($this->getName());
        $enabled = (bool) $value;

        if(null === $value && static::MODE === InputOption::VALUE_OPTIONAL)
        {
            $enabled = true;
        }

        if ($enabled && static::MODE !== InputOption::VALUE_NONE) {
            $this->value = $value;
        }

        return $enabled;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS ?: '';
    }

    public function preInstall(Version $version, InputInterface $input, OutputInterface $output)
    {

    }

    public function postInstall(Version $version, InputInterface $input, OutputInterface $output)
    {

    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getAlias() . ($this->value ? '=' . escapeshellarg($this->value) : '');
    }
}
