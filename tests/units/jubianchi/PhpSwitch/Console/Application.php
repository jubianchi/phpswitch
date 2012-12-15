<?php
namespace tests\units\jubianchi\PhpSwitch\Console;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Console\Application as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Application extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf('\\Symfony\\Component\\Console\\Application')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($object = new TestedClass())
            ->then
                ->string($object->getName())->isEqualTo('PhpSwitch')
                ->string($object->getVersion())->isEqualTo('0.1')
        ;
    }

    public function testSetConfiguration() {
        $this
            ->if($object = new TestedClass())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->then
                ->object($object->setConfiguration($config))->isIdenticalTo($object)
                ->object($object->getConfiguration())->isIdenticalTo($config)
        ;
    }

    public function testGetDownloader() {
        $this
            ->if($object = new TestedClass())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($config->getMockController()->get = uniqid())
            ->and($object->setConfiguration($config))
            ->then
                ->object($downloader = $object->getDownloader())->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Downloader')
                ->object($object->getDownloader())->isIdenticalTo($downloader)
        ;
    }

    public function testGetExtracter() {
        $this
            ->if($object = new TestedClass())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($config->getMockController()->get = uniqid())
            ->and($object->setConfiguration($config))
            ->then
                ->object($extracter = $object->getExtracter())->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Extracter')
                ->object($object->getExtracter())->isIdenticalTo($extracter)
        ;
    }

    public function testGetBuilder() {
        $this
            ->if($object = new TestedClass())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($config->getMockController()->get = uniqid())
            ->and($object->setConfiguration($config))
            ->then
                ->object($builder = $object->getBuilder())->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Builder')
                ->object($object->getBuilder())->isIdenticalTo($builder)
        ;
    }
}
