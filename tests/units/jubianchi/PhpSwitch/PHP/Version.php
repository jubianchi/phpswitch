<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use jubianchi\PhpSwitch\PHP\Version as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Version extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($version = uniqid())
            ->and($url = uniqid())
            ->then
                ->exception(function() use($version, $url) {
                    new TestedClass($version, $url);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Wrong PHP version %s', $version))
            ->if($version = '5.5.5')
            ->then
                ->object($object = new TestedClass($version, $url))
                ->string($object->getName())->isEqualTo('php')
                ->string($object->getVersion())->isEqualTo($version)
                ->string($object->getUrl())->isEqualTo('http://php.net/' . $url)
            ->if($url = 'http://' . ($host = uniqid()) . '/from/a/mirror')
            ->then
                ->object($object = new TestedClass($version, $url))
                ->string($object->getUrl())->isEqualTo('http://' . $host . '/from/%s/mirror')
			->if($name = uniqid())
			->then
				->object($object = new TestedClass($version, $url, $name))
				->string($object->getName())->isEqualTo($name)
				->string($object->getVersion())->isEqualTo($version)
        ;
    }

    public function testFromString()
    {
        $this
            ->if($version = uniqid())
            ->then
                ->exception(function() use($version) {
                    TestedClass::fromString(uniqid() . '-' . $version);
                })
                    ->isInstanceOf('\InvalidArgumentException')
                    ->hasMEssage(sprintf('Wrong PHP version %s', $version))
            ->if($version = '5.5.5')
            ->then
                ->object($object = TestedClass::fromString($version))->isInstanceOf('\\jubianchi\PhpSwitch\PHP\Version')
                ->string($object->getName())->isEqualTo(TestedClass::DEFAULT_NAME)
                ->string($object->getVersion())->isEqualTo($version)
            ->if($name = uniqid())
            ->then
                ->object($object = TestedClass::fromString($name . '-' . $version))->isInstanceOf('\\jubianchi\PhpSwitch\PHP\Version')
                ->string($object->getName())->isEqualTo($name)
                ->string($object->getVersion())->isEqualTo($version)
            ->if($name = uniqid() . '-' . uniqid())
            ->then
                ->object($object = TestedClass::fromString($name . '-' . $version))->isInstanceOf('\\jubianchi\PhpSwitch\PHP\Version')
                ->string($object->getName())->isEqualTo($name)
                ->string($object->getVersion())->isEqualTo($version)
        ;
    }

    public function test__toString()
    {
        $this
            ->if($version = '5.5.5')
            ->and($url = uniqid())
            ->and($object = new TestedClass($version, $url))
            ->then
                ->castToString($object)->isEqualTo('php-' . $version)
			->if($name = uniqid())
			->and($object = new TestedClass($version, $url, $name))
			->then
				->castToString($object)->isEqualTo($name . '-' . $version)
        ;
    }
}
