<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\PHP\Option\Enable;

class AtoumOption extends Option
{
    const ARG = 'atoum';

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
                sprintf('atoum compile options <comment>(%s)</comment>', $this)
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(
            ' ',
            array(
                new DisableAllOption(),
                new Enable\CLIOption(),
                new Enable\PHAROption(),
                new Enable\HashOption(),
                new Enable\JSONOption(),
                new Enable\XMLOption(),
                new Enable\SessionOption(),
                new Enable\TokenizerOption(),
                new Enable\PosixOption(),
                new Enable\DOMOption(),
                new Enable\MBStringOption()
            )
        );
    }
}
