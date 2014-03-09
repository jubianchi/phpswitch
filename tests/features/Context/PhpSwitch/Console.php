<?php
namespace jubianchi\PhpSwitch\Test\Context\PhpSwitch;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Context\Step;
use jubianchi\PhpSwitch\Test\Context\CLI;
use jubianchi\PhpSwitch\Test\Context\Sandboxed;

class Console extends CLI implements Sandboxed
{
    protected $root;
    protected $workspace;
    protected $home;
    protected $sandbox;

    public function setDirectories($root, $workspace, $home, $sandbox)
    {
        $this->root = $root;
        $this->workspace = $workspace;
        $this->home = $home;
        $this->sandbox = $sandbox;
    }

    public function iRun($command, $cwd = null, $env = null)
    {
        parent::iRun(
            $command,
            $cwd ?: getcwd(),
            $env ?: array(
                'PHPSWITCH_PREFIX' => $this->workspace,
                'PHPSWITCH_HOME' => $this->home,
            )
        );
    }

    /**
     * @Given /^I run \"(?P<command>[^\"]*)\" without env$/
     */
    public function iRunWithoutEnv($command, $cwd = null)
    {
        parent::iRun($command, $cwd ?: $this->sandbox);
    }

    /**
     * @Given /^I run the \"(?P<command>[^\"]*)\" command$/
     */
    public function iRunTheCommand($command)
    {
        $this->iRun(sprintf(__DIR__ . '/../../../../bin/phpswitch %s', $command));
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command$/
     */
    public function iRunTheInstallerCommand($command)
    {
        $this->iRun(
            sprintf(__DIR__ . '/../../../../bin/installer %s', $command),
            $this->sandbox,
            array(
                'COMPOSER_HOME' => getenv('HOME') . DIRECTORY_SEPARATOR . '.composer',
                'PHPSWITCH_PATH' => $this->workspace,
                'PHPSWITCH_SYMLINK' => $this->root,
            )
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command with env:$/
     */
    public function iRunTheInstallerCommandWithEnv($command, PyStringNode $env)
    {
        $this->iRun(
            sprintf(__DIR__ . '/../../../../bin/installer %s', $command),
            $this->sandbox,
            array_merge(array('PATH' => getenv('PATH')), parse_ini_string($env))
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command with PHP options \"(?P<options>[^\"]*)\"$/
     */
    public function iRunTheInstallerCommandWithPhpOptions($command, $options)
    {
        $this->iRun(
            sprintf('php %s ' . __DIR__ . '/../../../../bin/installer %s', $options, $command),
            $this->sandbox,
            array(
                'PHPSWITCH_PATH' => $this->workspace,
                'PHPSWITCH_SYMLINK' => $this->root,
            )
        );
    }

    /**
     * @Given /^I run the installer \"(?P<command>[^\"]*)\" command without env$/
     */
    public function iRunTheInstallerCommandWithoutEnv($command, $cwd = null)
    {
        parent::iRun(sprintf(__DIR__ . '/../../../../bin/installer %s', $command), $cwd ?: $this->sandbox);
    }
}
