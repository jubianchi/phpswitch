<?php
namespace tests\units\jubianchi\PhpSwitch\Console\Command;

use mock\jubianchi\PhpSwitch\Configuration;
use mageekguy\atoum;
use jubianchi\PhpSwitch\Console\Command\Command as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

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
            ->if(
                $object = new \mock\jubianchi\PhpSwitch\Console\Command\Command(),
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
                $application = new \mock\jubianchi\PhpSwitch\Console\Application($pimple),
                $config = new \mock\jubianchi\PhpSwitch\Configuration(uniqid()),
                $application->getMockController()->getConfiguration = $config,
                $object->setApplication($application)
            )
            ->then
                ->object($object->getConfiguration())->isIdenticalTo($config)
        ;
    }
}
