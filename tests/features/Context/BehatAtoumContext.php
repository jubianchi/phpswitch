<?php
use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class BehatAtoumContext extends BehatContext
{
    protected $assert;

    public function __construct()
    {
        $this->assert = new \mageekguy\atoum\asserter\generator();
    }
}