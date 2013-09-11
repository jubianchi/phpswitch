<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Event\Event;
use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\file;
use jubianchi\PhpSwitch\PHP\Extracter as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Extracter extends atoum\test
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
                ->object(new TestedClass($directory, $processBuilder))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Extracter')
        ;
    }

    public function testExtract()
    {
        $this
            ->if($dispatcher = new \mock\jubianchi\PhpSwitch\Event\Dispatcher())
            ->and($this->calling($dispatcher)->dispatch = function($name, $event) { return $event; })
            ->and($process = new \mock\Symfony\Component\Process\Process(uniqid()))
            ->and($this->calling($process)->run = 0)
            ->and($processFactory = new \mock\jubianchi\PhpSwitch\Process\Builder())
            ->and($this->calling($processFactory)->create = $processBuilder = new \mock\Symfony\Component\Process\ProcessBuilder())
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($directory = stream::get())
            ->and($directory->dir_opendir = true)
            ->and($archive = uniqid())
            ->and($options = new \mock\jubianchi\PhpSwitch\PHP\Option\OptionCollection(array()))
            ->and($this->calling($options)->__toString = $normalized = uniqid())
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($extracter = new \jubianchi\PhpSwitch\PHP\Extracter($directory, $processFactory, $dispatcher))
            ->then
                ->object($extracter->extract($version, $archive))->isIdenticalTo($extracter)
                ->mock($dispatcher)
                    ->call('dispatch')->withArguments(
                        'extract.before',
                         new Event(
                            'extract.before',
                            $extracter,
                            $args = array(
                                'version' => $version,
                                'archive' => $archive
                            )
                        )
                    )->once()
                    ->call('dispatch')->withArguments('extract.after', new Event('extract.after', $extracter, $args))->once()
        ;
    }
}
