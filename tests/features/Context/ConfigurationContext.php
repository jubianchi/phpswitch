<?php
use
    Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode
;

class ConfigurationContext extends BehatContext
{
    /**
     * @Given /^I have the following configuration in "(?P<path>[^\"]*)":$/
     */
    public function iHaveTheFollowingConfigurationInPhpswitch($path, PyStringNode $configuration)
    {
        file_put_contents($path, (string)$configuration);
    }
}
