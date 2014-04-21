<?php
namespace tests\units\jubianchi\PhpSwitch;

use mageekguy\atoum;
use mageekguy\atoum\mock\controller;
use \mock\jubianchi\PhpSwitch\Configuration as TestedClass;

require_once __DIR__ . '/../../bootstrap.php';

class Configuration extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf('\\IteratorAggregate')
        ;
    }

    public function testGet()
    {
        $this
            ->if(
                $controller = new controller(),
                $controller->read = array(),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->and($offset = uniqid())
            ->then
                ->exception(function() use($object, $offset) {
                    $object->get($offset);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Offset %s does not exist', $offset))
            ->if(
                $value = null,
                $controller = new controller(),
                $controller->read = array($offset => $value),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->then
                ->exception(function() use($object, $offset) {
                    $object->get($offset);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Offset %s does not exist', $offset))
            ->if($value = uniqid())
            ->if(
                $controller = new controller(),
                $controller->read = array($offset => $value),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->then
                ->variable($object->get($offset))->isIdenticalTo($value)
        ;
    }

    public function testSet()
    {
        $this
            ->if(
                $controller = new controller(),
                $controller->read = array(),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->and($offset = uniqid())
            ->and($value = uniqid())
            ->then
                ->object($object->set($offset, $value))->isIdenticalTo($object)
                ->variable($object->get($offset))->isIdenticalTo($value)
        ;
    }

    public function testGetIterator()
    {
        $this
            ->if(
                $controller = new controller(),
                $controller->read = array(),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->then
                ->object($object->getIterator())->isInstanceOf('\\RecursiveArrayIterator')
                ->array((array) $object->getIterator())->isEmpty()
            ->if(
                $controller = new controller(),
                $controller->read = $values = array(uniqid() => uniqid()),
                $object = new TestedClass(uniqid(), null, $controller)
            )
            ->then
                ->array((array) $object->getIterator())->isEqualTo($values)
        ;
    }
}
