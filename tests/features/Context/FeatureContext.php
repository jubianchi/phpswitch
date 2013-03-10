<?php
use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class FeatureContext extends BehatContext
{
    function __construct()
    {
        $this->useContext('phpswitch', new PhpSwitchContext());
        $this->useContext('cli', new CLIContext(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch/sandbox'));
        $this->useContext('fs', new FilesystemContext(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch'));
    }

    /**
     * @BeforeScenario ~@noinit
     */
    public static function init()
    {
        static::clean();

        if (false === is_dir(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch')) {
            mkdir(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch');
            mkdir(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch/home');
            mkdir(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch/sandbox');
        }
    }

    /**
     * @BeforeScenario @noinit
     * @AfterScenario
     */
    public static function clean()
    {
        if (is_dir(getcwd() . DIRECTORY_SEPARATOR . 'phpswitch')) {
            exec('rm -rf ' . getcwd() . DIRECTORY_SEPARATOR . 'phpswitch');
            clearstatcache();
        }
    }
}