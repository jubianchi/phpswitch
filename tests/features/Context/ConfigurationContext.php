<?php
use Behat\Gherkin\Node\PyStringNode;

class ConfigurationContext extends BehatAtoumContext
{
    /**
     * @Given /^I have the following configuration in "(?P<path>[^\"]*)":$/
     */
    public function iHaveTheFollowingConfigurationInPhpswitch($path, PyStringNode $configuration)
    {
        $this->assert
            ->integer(file_put_contents($path, (string)$configuration))
            ->isGreaterThan(0)
        ;
    }
}
