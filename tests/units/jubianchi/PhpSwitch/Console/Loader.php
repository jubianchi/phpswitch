<?php
namespace tests\units\jubianchi\PhpSwitch\Console;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Console\Loader as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Loader extends atoum\test
{
    public function testClass()
    {
        $this
            ->mockGenerator->shuntParentClassCalls()
            ->object(TestedClass::get(new \mock\jubianchi\PhpSwitch\Console\Command\Finder(uniqid(), uniqid())))
                ->isInstanceOf('jubianchi\\PhpSwitch\\Console\\Loader')
        ;
    }

    public function test__construct()
    {
        $this
            ->mockGenerator->shuntParentClassCalls()
            ->object(new TestedClass(new \mock\jubianchi\PhpSwitch\Console\Command\Finder(uniqid(), uniqid())))
        ;
    }

    public function testLoad()
    {
        $this
            ->mockGenerator->shuntParentClassCalls()
            ->if($finder = new \mock\jubianchi\PhpSwitch\Console\Command\Finder(uniqid(), uniqid()))
            ->and($this->calling($finder)->getIterator = new \ArrayIterator())
            ->and($object = new TestedClass($finder))
            ->and($application = new \mock\jubianchi\PhpSwitch\Console\Application(new \mock\Pimple()))
            ->then
                ->object($object->load($application))->isIdenticalTo($application)
        ;
    }
}
