<?php
namespace tests\units\jubianchi\PhpSwitch\Phar;

use mageekguy\atoum;
use mageekguy\atoum\mock;
use mageekguy\atoum\mock\streams;
use jubianchi\PhpSwitch\Phar\Builder as TestedClass;

class Builder extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->getFilters())->isInstanceOf('\\jubianchi\\PhpSwitch\\Phar\\Filter\\FilterCollection')
            ->if($builder = new TestedClass($filters = new \mock\jubianchi\PhpSwitch\Phar\Filter\FilterCollection()))
            ->then
                ->object($builder->getFilters())->isIdenticalTo($filters)
        ;
    }

    public function testAddFilter()
    {
        $this
            ->if($filters = new \mock\jubianchi\PhpSwitch\Phar\Filter\FilterCollection())
            ->and($builder = new TestedClass($filters))
            ->then
                ->object($builder->addFilter($filter = new \mock\jubianchi\PhpSwitch\Phar\Filter()))->isIdenticalTo($builder)
                ->mock($filters)
                    ->call('add')->withArguments($filter)->once()
            ->if($builder->addFilter($filter))
            ->then
                ->mock($filters)
                    ->call('add')->withArguments($filter)->twice()
        ;
    }

    public function testGetPhar()
    {
        if(false === in_array(ini_get('phar.readonly'), array(0, 'Off'))) {
            $this->skip('phar.readonly should be Off');
        }

        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->getPhar(uniqid() . '.phar'))->isInstanceOf('\\Phar')
        ;
    }

    public function testSetName()
    {
        $this
            ->if($builder = new \mock\jubianchi\PhpSwitch\Phar\Builder())
            ->then
                ->object($builder->setName($name = uniqid() . 'phar'))->isIdenticalTo($builder)
        ;
    }

    public function testSetBasedir()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->setBasedir(uniqid()))->isIdenticalTo($builder)
        ;
    }

    public function testAddFinder()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->addFinder(new \mock\Symfony\Component\Finder\Finder()))->isIdenticalTo($builder)
        ;
    }

    public function testAddFile()
    {
        $this
            ->if($builder = new TestedClass())
            ->and($file = streams\file::get())
            ->then
                ->object($builder->addFile($file))->isIdenticalTo($builder)
            ->if($file = uniqid())
            ->then
                ->exception(function() use ($builder, $file) {
                    $builder->addFile($file);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('File %s does not exist', $file))
        ;
    }

    public function testAddRaw()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->addRaw(uniqid(), uniqid()))->isIdenticalTo($builder)
        ;
    }

    public function testSetStub()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->object($builder->setStub(uniqid()))->isIdenticalTo($builder)
        ;
    }

    public function testCount()
    {
        $this
            ->if($builder = new TestedClass())
            ->then
                ->integer(count($builder))->isEqualTo(0)
            ->if($file = streams\file::get())
            ->and($builder->addFile((string) $file))
            ->then
                ->integer(count($builder))->isEqualTo(1)
            ->if($finder = new \mock\Symfony\Component\Finder\Finder())
            ->and($this->calling($finder)->count = 5)
            ->and($builder->addFinder($finder))
            ->then
                ->integer(count($builder))->isEqualTo(6)
        ;
    }

    public function testBuildPhar()
    {
        $test = $this;
        $getPhar = function() use (& $phar, $test){
            $test->mockGenerator->orphanize('__construct');
            $test->mockGenerator->shuntParentClassCalls();

            return $phar = new \mock\Phar();
        };

        $this
            ->if($builder = new \mock\jubianchi\PhpSwitch\Phar\Builder())
            ->and($this->calling($builder)->getPhar = $getPhar)
            ->and($builder->setName($name = uniqid() . 'phar'))
            ->then
                ->object($builder->buildPhar())->isIdenticalTo($phar)
                ->mock($builder)
                    ->call('getPhar')->withArguments($name)->once()
                ->mock($phar)
                    ->call('startBuffering')->once()
                    ->call('stopBuffering')->afterMethodCall('startBuffering')->once()
                    ->call('addFile')->never()
                    ->call('addFromString')->never()
            ->if($file = streams\file::get('foobar'))
            ->and($file->setContents($contents = 'contents'))
            ->and($builder->addFile((string) $file))
            ->then
                ->object($builder->buildPhar())->isIdenticalTo($phar)
                ->mock($phar)
                    ->call('addFile')->never()
                    ->call('addFromString')->withArguments((string) $file, $contents)->once()
            ->if($filters = new \mock\jubianchi\PhpSwitch\Phar\Filter\FilterCollection())
            ->and($this->calling($filters)->apply = $filtered = 'filtered')
            ->and($builder = new \mock\jubianchi\PhpSwitch\Phar\Builder($filters))
            ->and($this->calling($builder)->getPhar = $getPhar)
            ->and($builder->addFile((string) $file))
            ->then
                ->object($builder->buildPhar())->isIdenticalTo($phar)
                ->mock($filters)
                    ->call('apply')->withArguments($contents)->once()
                ->mock($phar)
                    ->call('addFromString')->withArguments((string) $file, $filtered)->once()
            ->if($builder->addRaw($path = uniqid(), $raw = uniqid()))
            ->then
                ->object($builder->buildPhar())->isIdenticalTo($phar)
                ->mock($phar)
                    ->call('addFromString')->withArguments($path, $raw)->once()
            ->if($builder->setStub($stub = uniqid()))
            ->then
                ->object($builder->buildPhar())->isIdenticalTo($phar)
                ->mock($phar)
                    ->call('setStub')->withArguments($stub)->once()
            ->if($callback = uniqid())
            ->then
                ->exception(function() use ($builder, $callback) {
                    $builder->buildPhar($callback);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage('Callback is not callable')
            ->if($callback = function() use (& $calls) {
                $calls[] = func_get_args();
            })
            ->then
                ->object($builder->buildPhar($callback))->isIdenticalTo($phar)
                ->array($calls)->isEqualTo(array(
                    array('2', '0', '0'),
                    array('2', '1', '0'),
                    array('2', '2', '1'),
                ))
        ;
    }
}
