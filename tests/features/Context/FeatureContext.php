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
     * @BeforeScenario ~@noinit
     */
    public static function init()
    {
        static::clean();

        if (false === is_dir('phpswitch')) {
            mkdir('phpswitch');
            mkdir('phpswitch/home');
            mkdir('phpswitch/sandbox');
        }
    }

    /**
     * @BeforeScenario @noinit
     * @AfterScenario
     */
    public static function clean()
    {
        if (is_dir('phpswitch')) {
            exec('rm -rf phpswitch');
            clearstatcache();
        }
    }
}