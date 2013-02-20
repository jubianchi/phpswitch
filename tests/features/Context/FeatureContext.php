<?php
use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class FeatureContext extends BehatContext
{
    function __construct()
    {
        $this->useContext('configuration', new ConfigurationContext());
        $this->useContext('cli', new CLIContext());
    }
}