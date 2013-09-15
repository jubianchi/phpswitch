<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Event\Event;
use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use jubianchi\PhpSwitch\PHP\Installer as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Installer extends atoum\test
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
            ->if($downloader = new \jubianchi\PhpSwitch\PHP\Downloader(uniqid()))
            ->and($extracter = new \jubianchi\PhpSwitch\PHP\Extracter(uniqid()))
            ->and($builder = new \jubianchi\PhpSwitch\PHP\Builder(uniqid()))
            ->then
                ->object($installer = new TestedClass($downloader, $extracter, $builder))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Installer')
        ;
    }

    public function testInstall()
    {
        $this
            ->if($root = stream::get())
            ->and($downloader = new \mock\jubianchi\PhpSwitch\PHP\Downloader(stream::getSubStream($root)))
            ->and($this->calling($downloader)->download = $downloader)
            ->and($this->calling($downloader)->getDestination = $archive = stream::get())
            ->and($extracter = new \mock\jubianchi\PhpSwitch\PHP\Extracter(stream::getSubStream($root)))
            ->and($this->calling($extracter)->extract = $extracter)
            ->and($this->calling($extracter)->getDestination = $source = stream::get())
            ->and($builder = new \mock\jubianchi\PhpSwitch\PHP\Builder($installDir = stream::getSubStream($root, uniqid())))
            ->and($this->calling($builder)->build = $builder)
            ->and($dispatcher = new \mock\jubianchi\PhpSwitch\Event\Dispatcher())
            ->and($this->calling($dispatcher)->dispatch = function($name, $event) { return $event; })
            ->and($options = new \mock\jubianchi\PhpSwitch\PHP\Option\OptionCollection(array()))
            ->and($this->calling($options)->__toString = $normalized = uniqid())
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($input = new \mock\Symfony\Component\Console\Input\InputInterface())
            ->and($output = new \mock\Symfony\Component\Console\Output\OutputInterface())
            ->and($process = new \mock\Symfony\Component\Process\Process(uniqid()))
            ->and($this->calling($process)->run = 0)
            ->and($processBuilder = new \mock\Symfony\Component\Process\ProcessBuilder())
            ->and($this->calling($processBuilder)->getProcess = $process)
            ->and($template = new \jubianchi\PhpSwitch\PHP\Template($version))
            ->and($template->setOptions($options))
            ->and($installer = new TestedClass($downloader, $extracter, $builder, $dispatcher))
            ->then
                ->object($installer->install($template, $mirror = uniqid(), null, $input, $output))
                ->mock($downloader)
                    ->call('download')->withArguments($version, $mirror)->once()
                ->mock($extracter)
                    ->call('extract')->withArguments($version, $archive)->once()
                ->mock($builder)
                    ->call('build')->withArguments($version, $source, $normalized, null)->once()
                ->mock($options)
                    ->call('preInstall')->withArguments($version, $input, $output)->once()
                    ->call('postInstall')->withArguments($version, $input, $output)->once()
                ->mock($dispatcher)
                    ->call('dispatch')->withArguments(
                        'install.before',
                        new Event(
                            'install.before',
                            $installer,
                            $args = array(
                                'version' => $version,
                                'mirror' => $mirror,
                                'jobs' => null,
                                'options' => $options,
                                'destination' => $builder->getDestination($version)
                            )
                        )
                    )->once()
                    ->call('dispatch')->withArguments('install.after', new Event('install.after', $installer, $args))->once()
                ->if($dir = stream::getSubStream($installDir, 'php-' . phpversion()))
                ->and($dir->dir_opendir = true)
                ->then
                    ->exception(function() use($installer, $template, $input, $output) {
                        $installer->install($template, uniqid(), null, $input, $output);
                    })
                        ->isInstanceOf('\\RuntimeException')
                        ->hasMessage(sprintf('PHP version %s is already installed', $version))
        ;
    }
}
