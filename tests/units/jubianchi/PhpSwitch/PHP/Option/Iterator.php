<?php
namespace tests\units\jubianchi\PhpSwitch\PHP\Option;

use mageekguy\atoum;
use jubianchi\PhpSwitch\PHP\Option\Iterator as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Iterator extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf('\\FilterIterator')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($iterator = new \mock\Iterator())
            ->and($directory = uniqid())
            ->then
                ->object(new TestedClass($iterator, $directory))
        ;
    }

    public function testAccept()
    {
        $this
            ->if($iterator = new \mock\Iterator())
            ->and($directory = uniqid())
            ->and($object = new \mock\jubianchi\PhpSwitch\PHP\Option\Iterator($iterator, $directory))
            ->and($this->getMockGenerator()->shuntParentClassCalls())
                ->and($reflector = new \mock\ReflectionClass(uniqid()))
                ->and($this->calling($reflector)->isInstantiable = true)
                ->and($this->calling($reflector)->isSubclassOf = true)
            ->and($this->getMockGenerator()->unshuntParentClassCalls())
            ->and($this->calling($object)->getReflector = $reflector)
            ->then
                ->boolean($object->accept())->isTrue()
            ->if($this->calling($reflector)->isInstantiable = false)
            ->then
                ->boolean($object->accept())->isFalse()
            ->if($this->calling($object)->getReflector->throw = new \ReflectionException())
            ->then
                ->boolean($object->accept())->isFalse()
        ;
    }

    public function testGetReflector()
    {
        $this
            ->if($iterator = new \mock\Iterator())
            ->and($directory = uniqid())
            ->and($object = new TestedClass($iterator, $directory))
            ->then
                ->object($object->getReflector('\\StdClass'))->isInstanceOf('\\ReflectionClass')
        ;
    }
}
