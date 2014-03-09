<?php

use Behat\Behat\Context\BehatContext;
use jubianchi\PhpSwitch\Test\Context;

require_once __DIR__ . '/../../../vendor/autoload.php';

class FeatureContext extends BehatContext
{
    private static $root;
    private static $workspace;
    private static $home;
    private static $sandbox;

    function __construct()
    {
        $this->useContext('phpswitch', new Context\PhpSwitch());
        $this->useContext('fs', new Context\Filesystem());
    }

    /**
     * @BeforeScenario
     */
    public function createSandbox()
    {
        static::$root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpswitch_' . time();
        static::$workspace = static::$root . DIRECTORY_SEPARATOR . 'workspace';
        static::$home = static::$root . DIRECTORY_SEPARATOR . 'home';
        static::$sandbox = static::$root . DIRECTORY_SEPARATOR . 'sandbox';

        if (false === is_dir(static::$home)) {
            mkdir(static::$home, 0777, true);
        }

        if (false === is_dir(static::$sandbox)) {
            mkdir(static::$sandbox, 0777, true);
        }

        $this->getSubcontext('phpswitch')->setDirectories(static::$root, static::$workspace, static::$home, static::$sandbox);
        $this->getSubcontext('fs')->setDirectories(static::$root, static::$workspace, static::$home, static::$sandbox);
    }

    /**
     * @AfterScenario ~@noclean
     */
    public function cleanSandbox()
    {
        if (is_dir(static::$root)) {
            exec('rm -rf ' . static::$root);
            clearstatcache();
        }
    }
}