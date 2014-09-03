<?php
namespace tests\units\jubianchi\PhpSwitch\Console;

use mageekguy\atoum;
use mock\jubianchi\PhpSwitch\Configuration;
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
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->string($object->getName())->isEqualTo('phpswitch')
                ->string($object->getVersion())->isEqualTo('0.1')
        ;
    }

    public function testGetDownloader()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $downloader) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        case 'app.php.downloader':
                            return $downloader = new \mock\jubianchi\PhpSwitch\PHP\Downloader(uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->object($object->getDownloader())->isIdenticalTo($downloader)
                ->mock($pimple)
                    ->call('offsetGet')->withArguments('app.php.downloader')->once()
        ;
    }

    public function testGetExtracter()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $extracter) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        case 'app.php.extracter':
                            return $extracter = new \mock\jubianchi\PhpSwitch\PHP\Extracter(uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->object($object->getExtracter())->isIdenticalTo($extracter)
                ->mock($pimple)
                    ->call('offsetGet')->withArguments('app.php.extracter')->once()
        ;
    }

    public function testGetBuilder()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $builder) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        case 'app.php.builder':
                            return $builder = new \mock\jubianchi\PhpSwitch\PHP\Builder(uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->object($object->getBuilder())->isIdenticalTo($builder)
                ->mock($pimple)
                    ->call('offsetGet')->withArguments('app.php.builder')->once()
        ;
    }

    public function testGetOptionFinder()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $generator = $this->mockGenerator,
                $this->calling($pimple)->offsetGet = function($id) use (& $finder, $generator) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        case 'app.php.option.finder':
                            $generator->shuntParentClassCalls();
                            return $finder = new \mock\jubianchi\PhpSwitch\PHP\Option\Finder(uniqid(), uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->object($object->getOptionFinder())->isIdenticalTo($finder)
                ->mock($pimple)
                    ->call('offsetGet')->withArguments('app.php.option.finder')->once()
        ;
    }

    public function testGetContainer()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $builder) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->then
                ->object($object->getContainer())->isIdenticalTo($pimple)
        ;
    }


    public function testGetService()
    {
        $this
            ->if(
                $pimple = new \mock\Pimple(),
                $this->calling($pimple)->offsetGet = function($id) use (& $exception, & $service, & $otherService, & $value) {
                    switch ($id) {
                        case 'app.config.local':
                            return new Configuration(uniqid());

                        case 'app.config.user':
                            return new Configuration(uniqid());

                        case $service:
                            throw $exception = new \Exception();

                        case $otherService:
                            return $value;

                        default:
                            return null;
                    }
                },
                $object = new TestedClass($pimple)
            )
            ->and($service = uniqid())
            ->then
                ->exception(function() use($object, $service) { $object->getService($service); })->isIdenticalTo($exception)
                ->mock($pimple)
                    ->call('offsetGet')->withArguments($service)->once()
            ->if(
                $otherService = uniqid(),
                $value = uniqid()
            )
            ->then
                ->string($object->getService($otherService))->isEqualTo($value)
        ;
    }
}
