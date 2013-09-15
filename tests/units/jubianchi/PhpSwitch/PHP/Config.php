<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\fs\file;
use jubianchi\PhpSwitch\PHP\Config as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Config extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($directory = uniqid())
            ->and($config = new TestedClass($directory))
            ->then
                ->string($config->getDirectory())->isEqualTo($directory)
        ;
    }

    public function testGetValue()
    {
        $this
            ->if($directory = uniqid())
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($config = new TestedClass($directory))
            ->then
               ->exception(function() use($config, $version) {
                    $config->getValue($version, uniqid());
               })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('PHP version %s is not installed', $version))
            ->if($directory = stream::get('installed'))
            ->and($directory->dir_opendir = true)
            ->and($versionDirectory = stream::getSubStream($directory, $version))
            ->and($versionDirectory->dir_opendir = true)
            ->and($varDirectory = stream::getSubStream($versionDirectory, 'var'))
            ->and($varDirectory->dir_opendir = true)
            ->and($dbDirectory = stream::getSubStream($varDirectory, 'db'))
            ->and($dbDirectory->dir_opendir = true)
            ->and($directory->dir_readdir[1] = $versionDirectory)
            ->and($file = file::getSubStream($dbDirectory, ($name = uniqid()) . '.ini'))
            ->and($file->setContents($name . '=' . $value = uniqid()))
            ->and($versionDirectory->dir_readdir[1] = $varDirectory)
            ->and($varDirectory->dir_readdir[1] = $dbDirectory)
            ->and($dbDirectory->dir_readdir[1] = $file)
            ->and($config = new TestedClass($directory))
            ->then
                ->string($config->getValue($version, $name))->isEqualTo($value)
        ;
    }

    public function testSetValue()
    {
        $this
            ->if($directory = stream::get('installed'))
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($directory->dir_opendir = true)
            ->and($versionDirectory = stream::getSubStream($directory, $version))
            ->and($versionDirectory->dir_opendir = true)
            ->and($varDirectory = stream::getSubStream($versionDirectory, 'var'))
            ->and($varDirectory->dir_opendir = true)
            ->and($dbDirectory = stream::getSubStream($varDirectory, 'db'))
            ->and($dbDirectory->dir_opendir = true)
            ->and($file = file::getSubStream($dbDirectory, ($name = uniqid()) . '.ini'))
            ->and($file->notExists())
            ->and($directory->dir_readdir[1] = $versionDirectory)
            ->and($versionDirectory->dir_readdir[1] = $varDirectory)
            ->and($varDirectory->dir_readdir[1] = $dbDirectory)
            ->and($dbDirectory->dir_readdir[1] = $file)
            ->and($config = new TestedClass($directory))
            ->then
                ->exception(function() use($config, $version) {
                    $config->setValue($version, uniqid(), uniqid());
                })
                    ->isInstanceOf('\\RuntimeException')
                    ->hasMessage('You don\'t have the required permission to edit configuration')
            ->if($file->exists())
            ->then
                ->object($config->setValue($version, $name, $value = uniqid()))->isIdenticalTo($config)
                ->string($file->getContents())->isEqualTo($name . ' = "' . $value . '"' . PHP_EOL)
        ;
    }
}
