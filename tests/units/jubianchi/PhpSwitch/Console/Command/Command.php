<?php
namespace tests\units\jubianchi\PhpSwitch\Console\Command;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Console\Command\Command as TestedClass;

require_once __DIR__ . '/../../../../../bootstrap.php';

class Command extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf('\\Symfony\\Component\\Console\\Command\\Command')
            ->string(TestedClass::NAME)->isEqualTo('command')
            ->string(TestedClass::DESC)->isEmpty()
        ;
    }

    public function test__construct()
    {
        $this
            ->if($object = new \mock\jubianchi\PhpSwitch\Console\Command\Command())
            ->then
                ->string($object->getName())->isEqualTo(TestedClass::NAME)
                ->string($object->getDescription())->isEqualTo(TestedClass::DESC)
        ;
    }

    public function testGetConfiguration()
    {
        $this
            ->if($object = new \mock\jubianchi\PhpSwitch\Console\Command\Command())
            ->and($application = new \mock\jubianchi\PhpSwitch\Console\Application())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($application->getMockController()->getConfiguration = $config)
            ->and($object->setApplication($application))
            ->then
                ->object($object->getConfiguration())->isIdenticalTo($config)
        ;
    }
}
