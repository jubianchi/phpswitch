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

    /**
     * @AfterSuite
     * @AfterScenario
     */
    public static function clean()
    {
        if (is_dir('phpswitch')) {
            exec('rm -rf phpswitch');
        }

        if (is_file('.phpswitch.yml')) {
            unlink('.phpswitch.yml');
        }
    }
}