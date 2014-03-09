<?php
namespace jubianchi\PhpSwitch\Test\Context;

class Filesystem extends Atoum implements Sandboxed
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

    public function locatePath($path)
    {
        return $this->root . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @Given /^The file "(?P<path>[^\"]*)" exists$/
     */
    public function theFileExists($path)
    {
        $this->assert->boolean(touch($this->locatePath($path)))->isTrue();
    }

    /**
     * @Then /^The file "(?P<path>[^\"]*)" should exist$/
     */
    public function theFileShouldExist($path)
    {
        $this->assert->boolean(is_file($this->locatePath($path)))->isTrue();
    }

    /**
     * @Given /^The directory "(?P<path>[^\"]*)" exists$/
     */
    public function theDirectoryExists($path)
    {
        $this->assert->boolean(mkdir($this->locatePath($path), 0777, true))->isTrue();
    }

    /**
     * @Then /^The directory "(?P<path>[^\"]*)" should exist$/
     */
    public function theDirectoryShouldExist($path)
    {
        $this->assert->boolean(is_dir($this->locatePath($path)))->isTrue();
    }

    /**
     * @Given /^I am in "(?P<path>[^\"]*)"$/
     * @Then /^I go to "(?P<path>[^\"]*)"$/
     */
    public function iAmIn($path)
    {
        $this->assert
            ->boolean(chdir($this->locatePath($path)))
            ->isTrue(
                sprintf(
                    'Could not change directory to %s',
                    $path
                )
            )
        ;
    }
}
