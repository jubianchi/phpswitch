<?php
class FilesystemContext extends BehatAtoumContext
{
    private $root;

    public function __construct($root)
    {
        parent::__construct();

        $this->root = $root;
    }

    /**
     * @Then /^The file "(?P<path>[^\"]*)" should exist$/
     */
    public function theFileShouldExist($path)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(is_file($path))->isTrue()
        ;
    }

    /**
     * @Given /^The directory "(?P<path>[^\"]*)" exists$/
     */
    public function theDirectoryExists($path)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(mkdir($path, 0777, true))->isTrue()
        ;
    }

    /**
     * @Then /^The directory "(?P<path>[^\"]*)" should exist$/
     */
    public function theDirectoryShouldExist($path)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(is_dir($path))->isTrue()
        ;
    }
}
