<?php
namespace tests\units\jubianchi\PhpSwitch\Phar;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Phar\Phar as TestedClass;
use jubianchi\PhpSwitch\Phar\Packager as PharPackager;

class Phar extends atoum\test
{
    public function test__construct()
    {
        $this
            ->and($archive = new \Phar(uniqid() . '.phar'))
            ->and($phar = new TestedClass($archive))
            ->then
                ->object($phar->getArchive())->isIdenticalTo($archive)
                ->object($phar->getPackager())->isEqualTo(new PharPackager())
            ->if($phar = new TestedClass($archive, $packager = new PharPackager()))
            ->then
                ->object($phar->getPackager())->isIdenticalTo($packager)
        ;
    }

    public function test__call()
    {
        $this
            ->if($this->mockGenerator->shunt('__construct'))
            ->and($archive = new \mock\Phar(uniqid() . '.phar'))
            ->and($this->calling($archive)->getFilename = $filename = uniqid())
            ->and($phar = new TestedClass($archive))
            ->then
                ->string($phar->getFilename())->isIdenticalTo($filename)
                ->mock($archive)
                    ->call('getFilename')->once()
        ;
    }
}
