<?php
namespace jubianchi\PhpSwitch\Test\Context\PhpSwitch;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\Step;
use jubianchi\PhpSwitch\Test\Context\Atoum;
use jubianchi\PhpSwitch\Test\Context\Sandboxed;

class Version extends Atoum implements Sandboxed
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
     * @Given /^The PHP version "(?P<version>[^"]*)" is installed$/
     */
    public function thePhpVersionIsInstalled($version)
    {
        return new Step\Given(sprintf('The directory "%s/installed/%s" exists', basename($this->workspace), $version));
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" should be installed$/
     */
    public function thePhpVersionShouldBeInstalled($version)
    {
        return new Step\Given(sprintf('The directory "%s/installed/%s" should exist', basename($this->workspace), $version));
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is globally enabled$/
     */
    public function thePhpVersionIsGloballyEnabled($version)
    {
        return new Step\Given(
            'I have the following global configuration:',
            new \Behat\Gherkin\Node\PyStringNode(
                'phpswitch:
                    version: ' . $version . PHP_EOL
            )
        );
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is locally enabled$/
     */
    public function thePhpVersionIsLocallyEnabled($version)
    {
        return new Step\Given(
            'I have the following local configuration:',
            new \Behat\Gherkin\Node\PyStringNode(
                'phpswitch:
                    version: ' . $version . PHP_EOL
            )
        );
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is installed and(?P<configuration>(?: locally| globally)?) enabled$/
     */
    public function thePhpVersionIsInstalledAndEnabled($version, $configuration = null)
    {
        return array(
            new Step\Given(sprintf('The PHP version "%s" is installed', $version)),
            new Step\Given(sprintf('The PHP version "%s" is %s enabled', $version, $configuration ?: 'locally'))
        );
    }
}
