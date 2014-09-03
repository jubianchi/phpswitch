<?php
namespace tests\units\jubianchi\PhpSwitch\Console;

use mock\jubianchi\PhpSwitch\Configuration;
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
            ->if(
                $finder = new \mock\jubianchi\PhpSwitch\Console\Command\Finder(uniqid(), uniqid()),
                $this->calling($finder)->getIterator = new \ArrayIterator(),
                $object = new TestedClass($finder),
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $exception) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        default:
                            return null;
                    }
                },
                $application = new \mock\jubianchi\PhpSwitch\Console\Application($pimple)
            )
            ->then
                ->object($object->load($application))->isIdenticalTo($application)
        ;
    }
}
