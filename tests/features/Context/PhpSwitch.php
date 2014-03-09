<?php

namespace jubianchi\PhpSwitch\Test\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

class PhpSwitch extends BehatContext implements Sandboxed
{
    protected $contexts;
    protected $root;
    protected $workspace;
    protected $home;
    protected $sandbox;

    function __construct()
    {
        $this->contexts = array(
            'phpswitch.configuration' => new PhpSwitch\Configuration(),
            'phpswitch.console' => new PhpSwitch\Console(),
            'phpswitch.version' => new PhpSwitch\Version()
        );

        foreach ($this->contexts as $alias => $context) {
            $this->useContext($alias, $context);
        }
    }

    public function setDirectories($root, $workspace, $home, $sandbox)
    {
        $this->root = $root;
        $this->workspace = $workspace;
        $this->home = $home;
        $this->sandbox = $sandbox;

        foreach ($this->contexts as $context) if ($context instanceof Sandboxed) {
            $context->setDirectories($root, $workspace, $home, $sandbox);
        }
    }

    /**
     * @Given /^phpswitch is initialized$/
     */
    function phpSwitchIsInitialized()
    {
        return new Step\Given('I run the "init" command');
    }
}
