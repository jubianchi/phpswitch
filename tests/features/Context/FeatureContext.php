<?php
use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class FeatureContext extends BehatContext
{
    function __construct()
    {
        $root = getcwd() . DIRECTORY_SEPARATOR . 'phpswitch';
        $sandbox = $root . DIRECTORY_SEPARATOR . 'sandbox';

        $this->useContext('phpswitch', new PhpSwitchContext());
        $this->useContext('fs', new FilesystemContext());
    }
}