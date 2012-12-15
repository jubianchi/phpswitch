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
            ->if($name = uniqid())
            ->and($url = uniqid())
            ->then
                ->exception(function() use($name, $url) {
                    new TestedClass($name, $url);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Wrong PHP version %s', $name))
            ->if($version = '5.5.5')
            ->if($name = 'php-' . $version)
            ->then
                ->object($object = new TestedClass($name, $url))
                ->string($object->getName())->isEqualTo($name)
                ->string($object->getVersion())->isEqualTo($version)
                ->string($object->getUrl())->isEqualTo('http://php.net/' . $url)
            ->if($url = 'http://' . ($host = uniqid()) . '/from/a/mirror')
            ->then
                ->object($object = new TestedClass($name, $url))
                ->string($object->getUrl())->isEqualTo('http://' . $host . '/from/%s/mirror')
        ;
    }

    public function test__toString()
    {
        $this
            ->if($name = 'php-5.5.5')
            ->and($url = uniqid())
            ->and($object = new TestedClass($name, $url))
            ->then
                ->castToString($object)->isEqualTo($name)
        ;
    }
}
