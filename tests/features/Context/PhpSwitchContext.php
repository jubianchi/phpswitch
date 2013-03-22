<?php
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\Step;

class PhpSwitchContext extends BehatAtoumContext
{
	private $root;

	public function __construct($root)
	{
		parent::__construct();

		$this->root = $root;
	}

    /**
     * @Given /^I have the following configuration in "(?P<path>[^\"]*)":$/
     */
    public function iHaveTheFollowingConfigurationInPhpswitch($path, PyStringNode $configuration)
    {
        $this->assert
            ->integer(file_put_contents($this->root . DIRECTORY_SEPARATOR . $path, (string)$configuration))
            ->isGreaterThan(0)
        ;
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is installed$/
     */
    public function thePhpVersionIsInstalled($version)
    {
        return new Step\Given(sprintf('The directory "prefix/installed/%s" exists', $version));
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is enabled$/
     */
    public function thePhpVersionIsEnabled($version)
    {
        $this->iHaveTheFollowingConfigurationInPhpswitch(
            '.phpswitch.yml',
            new \Behat\Gherkin\Node\PyStringNode(
                'phpswitch:
                    version: ' . $version . '
                '
            )
        );
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is installed and enabled$/
     */
    public function thePhpVersionIsInstalledAndEnabled($version)
    {
        return array(
            new Step\Given(sprintf('The PHP version "%s" is installed', $version)),
            new Step\Given(sprintf('The PHP version "%s" is enabled', $version))
        );
    }
}
