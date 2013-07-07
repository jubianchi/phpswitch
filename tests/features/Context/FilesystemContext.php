<?php
class FilesystemContext extends BehatAtoumContext
{
    /**
     * @Then /^The file "(?P<path>[^\"]*)" should exist$/
     */
    public function theFileShouldExist($path)
    {
        $path = $this->getMainContext()->getSubcontext('phpswitch')->getRoot() . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(is_file($path))->isTrue()
        ;
    }

    /**
     * @Given /^The directory "(?P<path>[^\"]*)" exists$/
     */
    public function theDirectoryExists($path)
    {
        $path = $this->getMainContext()->getSubcontext('phpswitch')->getRoot() . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(mkdir($path, 0777, true))->isTrue()
        ;
    }

    /**
     * @Then /^The directory "(?P<path>[^\"]*)" should exist$/
     */
    public function theDirectoryShouldExist($path)
    {
        $path = $this->getMainContext()->getSubcontext('phpswitch')->getRoot() . DIRECTORY_SEPARATOR . $path;

        $this->assert
            ->boolean(is_dir($path))->isTrue()
        ;
    }
}
