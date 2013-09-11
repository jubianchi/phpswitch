<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Event\Event;
use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
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
            ->and($this->calling($dispatcher)->dispatch = function($name, $event) { return $event; })
            ->and($process = new \mock\Symfony\Component\Process\Process(uniqid()))
            ->and($this->calling($process)->run = 0)
            ->and($this->calling($process)->isSuccessful = true)
            ->and($processFactory = new \mock\jubianchi\PhpSwitch\Process\Builder())
            ->and($this->calling($processFactory)->create = $processBuilder = new \mock\Symfony\Component\Process\ProcessBuilder())
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($directory = stream::get())
            ->and($directory->dir_opendir = true)
            ->and($source = uniqid())
            ->and($options = new \jubianchi\PhpSwitch\PHP\Option\OptionCollection(array()))
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($builder = new \jubianchi\PhpSwitch\PHP\Builder($directory, $processFactory, $dispatcher))
            ->then
                ->object($builder->build($version, $source, $options))->isIdenticalTo($builder)
                ->mock($dispatcher)
                    ->call('dispatch')->withArguments(
                        'build.before',
                        new Event(
                            'build.before',
                            $builder,
                            $args = array(
                                'version' => $version,
                                'source' => $source,
                                'option' => $options,
                                'jobs' => null,
                                'prefix' => $prefix = $builder->getDestination($version)
                            )
                        )
                    )->once()
                    ->call('dispatch')->withArguments('build.after', new Event('build.after', $builder, $args))->once()
                ->mock($processFactory)
                    ->call('create')->withArguments(
                        array(
                            './configure',
                            '--prefix=' . $prefix,
                            '--with-config-file-path=' . $prefix . '/etc',
                            '--with-config-file-scan-dir=' . $prefix . '/var/db',
                            '--with-pear=' . $prefix . '/lib/php'
                        )
                    )->once()
            ->if($makefile = stream::getSubStream($directory, 'Makefile'))
            ->and($makefile->file_get_contents = uniqid())
            ->and($builder = new \jubianchi\PhpSwitch\PHP\Builder($directory, $processFactory, $dispatcher))
            ->then
                ->object($builder->build($version, $source, $options))->isIdenticalTo($builder)
                ->mock($processFactory)
                    ->call('create')->withArguments(array('make', 'clean'))->once()
        ;
    }
}
