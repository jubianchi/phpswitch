<?php
use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class FeatureContext extends BehatContext
{
    function __construct()
    {
        $root = getcwd() . DIRECTORY_SEPARATOR . 'phpswitch';
        $sandbox = $root . DIRECTORY_SEPARATOR . 'sandbox';

        $this->useContext('phpswitch', new PhpSwitchContext($sandbox));
        $this->useContext('cli', new CLIContext($sandbox));
        $this->useContext('fs', new FilesystemContext($root));
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