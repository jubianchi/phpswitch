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
use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

class OptionCollection implements OptionInterface, \Countable, \Iterator
{
    /** @var \jubianchi\PhpSwitch\PHP\Option\OptionInterface[] */
    protected $options = array();

    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->addOptions($options);
        }
    }

	public function setCommand(Command $command)
	{
		foreach($this->options as $option) {
			$option->setCommand($command);
		}

		return $this;
	}

    public function addOptions(array $options)
    {
        foreach ($options as $option) {
			$this->addOption($option);
		}

        return $this;
    }

	public function addOption(Option $option)
	{
		if (false === array_key_exists($option->getName(), $this->options)) {
			$this->options[$option->getName()] = $option;
		}

		return $this;
	}

    public function merge(OptionCollection $collection)
    {
        return $this->addOptions($collection->options);
    }

    public function preInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        foreach ($this->options as $option) {
            $option->preInstall($version, $input, $output);
        }
    }

    public function postInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        foreach ($this->options as $option) {
            $option->postInstall($version, $input, $output);
        }
    }

    public function count()
    {
        return count($this->options);
    }

    public function __toString()
    {
        return implode(' ', $this->options);
    }

	public function current()
	{
		return current($this->options);
	}

	public function next()
	{
		next($this->options);
	}

	public function key()
	{
		return key($this->options);
	}

	public function valid()
	{
		return $this->key() !== null;
	}

	public function rewind()
	{
		reset($this->options);
	}
}
