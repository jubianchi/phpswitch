<?php
namespace jubianchi\PhpSwitch\Test\Context\PhpSwitch;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\Step;
use jubianchi\PhpSwitch\Test\Context\Atoum;
use jubianchi\PhpSwitch\Test\Context\Sandboxed;

class Configuration extends Atoum implements Sandboxed
{
    protected $root;
    protected $workspace;
    protected $home;
    protected $sandbox;

    public function setDirectories($root, $workspace, $home, $sandbox)
    {
        $this->root = $root;
        $this->workspace = $workspace;
        $this->home = $home;
        $this->sandbox = $sandbox;
    }

    /**
     * @Given /^I have the following local configuration:$/
     */
    public function iHaveTheFollowingLocalConfiguration(PyStringNode $configuration)
    {
        return $this->iHaveTheFollowingConfigurationInPath(basename($this->sandbox) . DIRECTORY_SEPARATOR . '.phpswitch.yml', $configuration);
    }

    /**
     * @Given /^I have the following global configuration:$/
     */
    public function iHaveTheFollowingGlobalConfiguration(PyStringNode $configuration)
    {
        return $this->iHaveTheFollowingConfigurationInPath(basename($this->home) . DIRECTORY_SEPARATOR . '.phpswitch.yml', $configuration);
    }

    /**
     * @Given /^I have the following configuration in "(?P<path>[^\"]*)":$/
     */
    public function iHaveTheFollowingConfigurationInPath($path, PyStringNode $configuration)
    {
        $this->assert
            ->integer(file_put_contents($this->root . DIRECTORY_SEPARATOR . $path, (string) $configuration))
            ->isGreaterThan(0)
        ;
    }
}
