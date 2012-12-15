<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\PHP\Option\Enable;
use jubianchi\PhpSwitch\PHP\Option\With;

class DefaultOption extends Option
{
    const ARG = 'default';

    public function applyArgument(Command $command)
    {
        if (static::ARG !== null && false === $command->getDefinition()->hasArgument(static::ARG))
        {
            $command->addOption(
                static::ARG,
                null,
                InputOption::VALUE_NONE,
                sprintf('Default compile options <comment>(%s)</comment>', $this)
            );
        }

        return $this;
    }

    public function __toString()
    {
        return implode(
            ' ',
            array(
                new DisableAllOption(),
                new Enable\CTypeOption(),
                new Enable\DOMOption(),
                new Enable\JSONOption(),
                new Enable\PHAROption(),
                new Enable\SimpleXMLOption(),
                new Enable\XMLOption(),
                new Enable\TokenizerOption(),
                new With\XSLOption()
            )
        );
    }
}
