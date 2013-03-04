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
