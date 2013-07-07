<?php
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\Step;

class PhpSwitchContext extends CLIContext
{
    private static $root;
    private static $workspace;
    private static $home;
    private static $sandbox;

    public function __construct()
    {
        parent::__construct();
    }

    public function getRoot()
    {
        return static::$root;
    }

    /**
     * @BeforeScenario ~@noinit
     */
    public static function beforeScenario()
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
    }

    /**
     * @BeforeScenario @noinit
     * @AfterScenario
     */
    public static function afterScenario()
    {
        if (is_dir(static::$root)) {
            exec('rm -rf ' . static::$root);
            clearstatcache();
        }
    }

    public function iRun($command, $cwd = null, $env = null)
    {
        parent::iRun(
            $command,
            $cwd ?: static::$sandbox,
            $env ?: array(
                'PHPSWITCH_PREFIX' => static::$workspace,
                'PHPSWITCH_HOME' => static::$home,
            )
        );
    }

    /**
     * @Given /^I run \"(?P<command>[^\"]*)\" without env$/
     */
    public function iRunWithoutEnv($command, $cwd = null)
    {
        parent::iRun($command, $cwd ?: static::$sandbox);
    }


    /**
     * @Given /^I run the \"(?P<command>[^\"]*)\" command$/
     */
    public function iRunTheCommand($command)
    {
        $this->iRun(sprintf(__DIR__ . '/../../../bin/phpswitch %s', $command));
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command$/
     */
    public function iRunTheInstallerCommand($command)
    {
        $this->iRun(
            sprintf(__DIR__ . '/../../../bin/installer %s', $command),
            static::$sandbox,
            array(
                'PHPSWITCH_GIT_URL' => realpath(__DIR__ . '/../../..'),
                'COMPOSER_HOME' => getenv('HOME') . DIRECTORY_SEPARATOR . '.composer',
                'PHPSWITCH_PATH' => static::$workspace,
                'PHPSWITCH_SYMLINK' => static::$root,
            )
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command with env:$/
     */
    public function iRunTheInstallerCommandWithEnv($command, PyStringNode $env)
    {
        $this->iRun(
            sprintf(__DIR__ . '/../../../bin/installer %s', $command),
            static::$sandbox,
            array_merge(array('PATH' => getenv('PATH')), parse_ini_string($env))
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command with PHP options \"(?P<options>[^\"]*)\"$/
     */
    public function iRunTheInstallerCommandWithPhpOptions($command, $options)
    {
        $this->iRun(
            sprintf('php %s ' . __DIR__ . '/../../../bin/installer %s', $options, $command),
            static::$sandbox,
            array(
                'PHPSWITCH_PATH' => static::$workspace,
                'PHPSWITCH_SYMLINK' => static::$root,
            )
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command without env$/
     */
    public function iRunTheInstallerCommandWithoutEnv($command, $cwd = null)
    {
        parent::iRun(sprintf(__DIR__ . '/../../../bin/installer %s', $command), $cwd ?: static::$sandbox);
    }

    /**
     * @Given /^I have the following configuration in "(?P<path>[^\"]*)":$/
     */
    public function iHaveTheFollowingConfigurationInPath($path, PyStringNode $configuration)
    {
        $this->assert
            ->integer(file_put_contents(static::$root . DIRECTORY_SEPARATOR . $path, (string)$configuration))
            ->isGreaterThan(0)
        ;
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is installed$/
     */
    public function thePhpVersionIsInstalled($version)
    {
        return new Step\Given(sprintf('The directory "workspace/installed/%s" exists', $version));
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" should be installed$/
     */
    public function thePhpVersionShouldBeInstalled($version)
    {
        return new Step\Given(sprintf('The directory "workspace/installed/%s" should exist', $version));
    }

    /**
     * @Given /^The PHP version "(?P<version>[^"]*)" is enabled$/
     */
    public function thePhpVersionIsEnabled($version)
    {
        $this->iHaveTheFollowingConfigurationInPath(
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
