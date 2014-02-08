<?php
namespace tests\units\jubianchi\PhpSwitch;

use mageekguy\atoum;
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
            ->if($object = new TestedClass(uniqid()))
            ->and($this->calling($object)->read = array())
            ->and($offset = uniqid())
            ->then
                ->exception(function() use($object, $offset) {
                    $object->get($offset);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Offset %s does not exist', $offset))
            ->if($value = null)
            ->and($this->calling($object)->read = array($offset => $value))
            ->then
                ->exception(function() use($object, $offset) {
                    $object->get($offset);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Offset %s does not exist', $offset))
            ->if($value = uniqid())
            ->and($this->calling($object)->read = array($offset => $value))
            ->then
                ->variable($object->get($offset))->isIdenticalTo($value)
        ;
    }

    public function testSet()
    {
        $this
            ->given($dumper = new \mock\jubianchi\PhpSwitch\Configuration\Dumper())
            ->and($this->calling($dumper)->dump = $dumper)
            ->if($object = new TestedClass(uniqid(), $dumper))
            ->and($this->calling($object)->read = array())
            ->and($offset = uniqid())
            ->and($value = uniqid())
            ->then
                ->object($object->set($offset, $value))->isIdenticalTo($object)
                ->mock($dumper)
                    ->call('dump')->withArguments($object->getPath(), array(TestedClass::ROOT => array($offset => $value)))->once()
        ;
    }

    public function testGetIterator()
    {
        $this
            ->if($object = new TestedClass(uniqid()))
            ->and($this->calling($object)->read = array())
            ->then
                ->object($object->getIterator())->isInstanceOf('\\RecursiveArrayIterator')
                ->array((array) $object->getIterator())->isEmpty()
            ->if($this->calling($object)->read = $values = array(uniqid() => uniqid()))
            ->then
                ->array((array) $object->getIterator())->isEqualTo($values)
        ;
    }

    public function testSetDumper()
    {
        $this
            ->if($object = new TestedClass(uniqid()))
            ->and($this->calling($object)->read = array())
            ->and($dumper = new \mock\jubianchi\PhpSwitch\Configuration\Dumper())
            ->then
                ->object($object->setDumper($dumper))->isIdenticalTo($object)
                ->object($object->getDumper())->isIdenticalTo($dumper)
        ;
    }

    public function testGetDumper()
    {
        $this
            ->if($object = new TestedClass(uniqid()))
            ->and($this->calling($object)->read = array())
            ->then
                ->object($object->getDumper())->isInstanceOf('\\jubianchi\\PhpSwitch\\Configuration\\Dumper')
            ->if($dumper = new \mock\jubianchi\PhpSwitch\Configuration\Dumper())
            ->and($object->setDumper($dumper))
            ->then
                ->object($object->getDumper())->isIdenticalTo($dumper)
        ;
    }
}
