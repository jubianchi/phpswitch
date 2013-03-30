<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class Option implements OptionInterface
{
    const ARG = null;
    const ALIAS = null;
    const DESC = null;
    const MODE = InputOption::VALUE_NONE;
    const DEFAULT_VALUE = null;

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

    public function setValue($value = null)
    {
        if (null === $value && $this->getMode() === InputOption::VALUE_OPTIONAL) {
            $this->value = $this->getDefault();
        }

        if ($this->getMode() !== InputOption::VALUE_NONE) {
            $this->value = $value;
        }

        return $this;
    }

    public function getValue()
    {
        if (null === $this->value && $this->isEnabled()) {
            return $this->getDefault();
        }

        return $this->value;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return static::MODE;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return (static::DESC ?: 'Enables ' . static::ARG) . sprintf($this->getAlias() ? ' <comment>(%s)</comment>' : '', $this->getAlias());
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return static::DEFAULT_VALUE;
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
                $this->getDesc(),
                null
            );
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return bool
     */
    public function isEnabled(InputInterface $input = null)
    {
        $value = null;
        $enabled = false;

        if (null !== $input) {
            $value = $input->getOption($this->getName());
            $enabled = (bool) $value;
        }

        if (null === $value && $this->getMode() === InputOption::VALUE_OPTIONAL) {
            $enabled = in_array('--' . $this->getName(), $_SERVER['argv']);
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
        return $this->getAlias() . ($this->getValue() ? '=' . $this->getValue() : '');
    }
}
