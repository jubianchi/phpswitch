<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

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
            ->and($process = new \mock\Symfony\Component\Process\Process(uniqid()))
            ->and($this->calling($process)->run = 0)
            ->and($processBuilder = new \mock\jubianchi\PhpSwitch\Process\Builder())
            ->and($this->calling($processBuilder)->get = $processBuilder)
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($directory = stream::get())
            ->and($directory->dir_opendir = true)
            ->and($archive = uniqid())
            ->and($options = new \mock\jubianchi\PhpSwitch\PHP\Option\OptionCollection(array()))
            ->and($this->calling($options)->normalize = $normalized = uniqid())
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($extracter = new \mock\jubianchi\PhpSwitch\PHP\Extracter($directory, $processBuilder, $dispatcher))
            ->then
                ->object($extracter->extract($version, $archive))->isIdenticalTo($extracter)
                ->mock($extracter)
                    ->call('emit')->withArguments(
                        'extract.before',
                        $args = array(
                            'version' => $version,
                            'archive' => $archive
                        )
                    )->once()
                    ->call('emit')->withArguments('extract.after', $args)->once()
        ;
    }
}
