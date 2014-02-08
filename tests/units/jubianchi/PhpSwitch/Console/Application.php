<?php
namespace tests\units\jubianchi\PhpSwitch\Console;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Configuration;
use jubianchi\PhpSwitch\Console\Application as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

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
            ->if($object = new TestedClass(new \mock\Pimple()))
            ->then
                ->string($object->getName())->isEqualTo('phpswitch')
                ->string($object->getVersion())->isEqualTo('0.1')
        ;
    }

    public function testGetDownloader()
    {
        $this
            ->given($container = new \mock\Pimple())
            ->if($object = new TestedClass($container))
            ->and($container->getMockController()->offsetGet = $downloader = new \mock\jubianchi\PhpSwitch\PHP\Downloader(uniqid()))
            ->then
                ->object($object->getDownloader())->isIdenticalTo($downloader)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.downloader')->once()
        ;
    }

    public function testGetExtracter()
    {
        $this
            ->given($container = new \mock\Pimple())
            ->if($object = new TestedClass($container))
            ->and($container->getMockController()->offsetGet = $extracter = new \mock\jubianchi\PhpSwitch\PHP\Extracter(uniqid()))
            ->then
                ->object($object->getExtracter())->isIdenticalTo($extracter)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.extracter')->once()
        ;
    }

    public function testGetBuilder()
    {
        $this
            ->given($container = new \mock\Pimple())
            ->if($object = new TestedClass($container))
            ->and($container->getMockController()->offsetGet = $builder = new \mock\jubianchi\PhpSwitch\PHP\Builder(uniqid()))
            ->then
                ->object($object->getBuilder())->isIdenticalTo($builder)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.builder')->once()
        ;
    }

    public function testGetOptionFinder()
    {
        $this
            ->given($container = new \mock\Pimple())
            ->if($object = new TestedClass($container))
            ->mockGenerator->shuntParentClassCalls()
            ->and($container->getMockController()->offsetGet = $builder = new \mock\jubianchi\PhpSwitch\PHP\Option\Finder(uniqid(), uniqid()))
            ->then
                ->object($object->getOptionFinder())->isIdenticalTo($builder)
                ->mock($container)
                    ->call('offsetGet')->withArguments('app.php.option.finder')->once()
        ;
    }

    public function testGetContainer()
    {
        $this
            ->if($object = new TestedClass($container = new \mock\Pimple()))
            ->then
                ->object($object->getContainer())->isIdenticalTo($container)
        ;
    }


    public function testGetService()
    {
        $this
            ->given($container = new \mock\Pimple())
            ->if($object = new TestedClass($container))
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
