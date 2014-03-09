<?php
namespace jubianchi\PhpSwitch\Test\Context;

use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../../vendor/autoload.php';

class Atoum extends BehatContext
{
    protected $assert;

    public function __construct()
    {
        $this->assert = new \mageekguy\atoum\asserter\generator();
    }
}