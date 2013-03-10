<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\file;
use jubianchi\PhpSwitch\PHP\Builder as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Builder extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf('\\jubianchi\\PhpSwitch\\Event\\Emitter')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($directory = uniqid())
            ->and($processBuilder = new \jubianchi\PhpSwitch\Process\Builder())
            ->then
                ->object(new TestedClass($directory, $processBuilder))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Builder')
        ;
    }

    public function testBuild()
    {
        $this
            ->if($dispatcher = new \mock\jubianchi\PhpSwitch\Event\Dispatcher())
            ->and($process = new \mock\Symfony\Component\Process\Process(uniqid()))
            ->and($this->calling($process)->run = 0)
            ->and($processBuilder = new \mock\jubianchi\PhpSwitch\Process\Builder())
            ->and($this->calling($processBuilder)->get = $processBuilder)
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($directory = stream::get())
            ->and($directory->dir_opendir = true)
            ->and($source = uniqid())
            ->and($options = new \mock\jubianchi\PhpSwitch\PHP\Option\OptionCollection(array()))
            ->and($this->calling($options)->normalize = $normalized = uniqid())
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($builder = new \mock\jubianchi\PhpSwitch\PHP\Builder($directory, $processBuilder, $dispatcher))
            ->then
                ->object($builder->build($version, $source, $options))->isIdenticalTo($builder)
                ->mock($builder)
                    ->call('configure')->withArguments($version, $source, $options)->once()
                    ->call('make')->withArguments($source, null)->once()
                    ->call('emit')->withArguments(
                        'build.before',
                        $args = array(
                            'version' => $version,
                            'source' => $source,
                            'option' => $options,
                            'jobs' => null,
                            'prefix' => $builder->getDestination($version)
                        )
                    )->once()
                    ->call('emit')->withArguments('build.after', $args)->once()
            ->if($processBuilder = new \mock\jubianchi\PhpSwitch\Process\Builder())
            ->and($this->calling($processBuilder)->get = $processBuilder)
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($makefile = stream::getSubStream($directory, 'Makefile'))
            ->and($makefile->file_get_contents = uniqid())
            ->and($builder = new \mock\jubianchi\PhpSwitch\PHP\Builder($directory, $processBuilder, $dispatcher))
            ->then
                ->object($builder->build($version, $source, $options))->isIdenticalTo($builder)
                ->mock($builder)
                    ->call('clean')->withArguments($source)->once()
        ;
    }
}
