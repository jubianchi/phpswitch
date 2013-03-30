<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\PHP\Option\OptionCollection;

class Template
{
    /** @var \jubianchi\PhpSwitch\PHP\Version */
    protected $version;

    /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection */
    protected $options;

    /** @var array() */
    protected $configs = array();

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setOptions(OptionCollection $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setConfigs(array $configs)
    {
        $this->configs = $configs;

        return $this;
    }

    public function getConfigs()
    {
        return $this->configs;
    }

	public function getName()
	{
		return str_replace('.', '-', $this->version);
	}
}
