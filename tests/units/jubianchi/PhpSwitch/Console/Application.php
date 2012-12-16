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
                ->string($object->getName())->isEqualTo('phpswitch')
                ->string($object->getVersion())->isEqualTo('0.1')
        ;
    }

    public function testSetConfiguration()
    {
        $this
            ->if($object = new TestedClass())
            ->and($config = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->then
                ->object($object->setConfiguration($config))->isIdenticalTo($object)
                ->object($object->getConfiguration())->isIdenticalTo($config)
        ;
    }

    public function testGetDownloader()
    {
        $this
            ->if($object = new TestedClass())
            ->and($container = new \mock\Pimple())
            ->and($container->getMockController()->offsetGet = $downloader = new \mock\jubianchi\PhpSwitch\PHP\Downloader(uniqid()))
            ->and($object->setContainer($container))
            ->then
                ->object($object->getDownloader())->isIdenticalTo($downloader)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.downloader')->once()
        ;
    }

    public function testGetExtracter()
    {
        $this
            ->if($object = new TestedClass())
            ->and($container = new \mock\Pimple())
            ->and($container->getMockController()->offsetGet = $extracter = new \mock\jubianchi\PhpSwitch\PHP\Extracter(uniqid()))
            ->and($object->setContainer($container))
            ->then
                ->object($object->getExtracter())->isIdenticalTo($extracter)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.extracter')->once()
        ;
    }

    public function testGetBuilder()
    {
        $this
            ->if($object = new TestedClass())
            ->and($container = new \mock\Pimple())
            ->and($container->getMockController()->offsetGet = $builder = new \mock\jubianchi\PhpSwitch\PHP\Builder(uniqid()))
            ->and($object->setContainer($container))
            ->then
                ->object($object->getBuilder())->isIdenticalTo($builder)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.builder')->once()
        ;
    }

    public function testGetOptionFinder()
    {
        $this
            ->if($object = new TestedClass())
            ->and($container = new \mock\Pimple())
            ->mockGenerator->shuntParentClassCalls()
            ->and($container->getMockController()->offsetGet = $builder = new \mock\jubianchi\PhpSwitch\PHP\Option\Finder(uniqid(), uniqid()))
            ->and($object->setContainer($container))
            ->then
                ->object($object->getOptionFinder())->isIdenticalTo($builder)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.option.finder')->once()
        ;
    }

    public function testGetContainer()
    {
        $this
            ->if($object = new TestedClass())
            ->then
                ->variable($object->getContainer())->isNull()
            ->if($container = new \mock\Pimple())
            ->and($object->setContainer($container))
            ->then
                ->object($object->getContainer())->isIdenticalTo($container)
        ;
    }

    public function testSetContainer()
    {
        $this
            ->if($object = new TestedClass())
            ->and($container = new \mock\Pimple())
            ->then
                ->object($object->setContainer($container))->isIdenticalTo($object)
                ->object($object->getContainer())->isIdenticalTo($container)
        ;
    }

    public function testGetService()
    {
        $this
            ->if($object = new TestedClass())
            ->then
                ->exception(function() use($object) {
                    $object->getService(uniqid());
                })
                    ->isInstanceOf('\\RuntimeException')
                    ->hasMessage('No service container defined')
            ->if($container = new \mock\Pimple())
            ->and($object->setContainer($container))
            ->and($service = uniqid())
            ->then
                ->exception(function() use($object, $service) {
                    $object->getService($service);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Identifier "%s" is not defined.', $service))
                ->mock($container)
                    ->call('offsetGet')->withArguments($service)->once()
            ->if($container[$service] = $value = uniqid())
            ->then
                ->string($object->getService($service))->isEqualTo($value)
        ;
    }
}
