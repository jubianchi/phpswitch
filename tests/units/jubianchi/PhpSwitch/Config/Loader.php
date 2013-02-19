<?php
namespace tests\units\jubianchi\PhpSwitch\Config;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\file;
use jubianchi\PhpSwitch\Config\Loader as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Loader extends atoum\test
{
    public function testLoad()
    {
        $this
            ->if($directory = stream::get('directory'))
            ->and($file = file::getSubStream($directory, $name = uniqid()))
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($configuration->getMockController()->getValues = array())
            ->and($validator = new \mock\jubianchi\PhpSwitch\Config\Validator())
            ->and($validator->getMockController()->validate = function($values) { return $values; })
            ->and($dumper = new \mock\jubianchi\PhpSwitch\Config\Dumper($directory))
            ->and($dumper->getMockController()->dump = $dumper)
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader(array($directory), $validator))
            ->then
                ->object($object->load($name, $configuration, $dumper))->isIdenticalTo($configuration)
            ->if($file->setContents("phpswitch:\n    version: php-5.5.5\n"))
            ->then
                ->object($object->load($name, $configuration, $dumper))->isIdenticalTo($configuration)
                ->mock($configuration)
                    ->call('setValues')->withArguments($validator->validate(array('phpswitch' => array('version' => 'php-5.5.5'))))->once()
            ->if($otherDirectory = stream::get('otherDirectory'))
            ->and($otherFile = file::getSubStream($otherDirectory, $name))
            ->and($otherFile->setContents("phpswitch:\n    version: php-5.4.4\n    mirror: foobar\n"))
            ->and($otherFile->setContents("phpswitch:\n    version: php-5.4.4\n    mirror: foobar\n"))
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader(array($directory, $otherDirectory), $validator))
            ->then
                ->object($object->load($name, $configuration, $dumper))->isIdenticalTo($configuration)
                ->mock($configuration)
                    ->call('setValues')->withArguments($validator->validate(array('phpswitch' => array('version' => 'php-5.4.4', 'mirror' => 'foobar'))))->once()
            ->if($subDirectory = stream::getSubStream($otherDirectory, 'subDirectory'))
            ->and($thirdFile = file::getSubStream($subDirectory, $name))
            ->and($thirdFile->setContents("phpswitch:\n    version: php-5.4.5\n"))
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader(array($directory, (string) $subDirectory => TestedClass::DIRECTORY_BUBBLE), $validator))
            ->then
                ->object($object->load($name, $configuration, $dumper))->isIdenticalTo($configuration)
                ->mock($configuration)
                    ->call('setValues')->withArguments($validator->validate(array('phpswitch' => array('version' => 'php-5.4.5', 'mirror' => 'foobar'))))->once()
        ;
    }

    public function testParse()
    {
        $this
            ->if($directory = stream::get('directory'))
            ->and($file = stream::getSubStream($directory, $name = uniqid()))
            ->and($file->file_get_contents = "phpswitch:\n    version: 0.1.1")
            ->and($directory->readdir[1] = $file)
            ->and($validator = new \mock\jubianchi\PhpSwitch\Config\Validator())
            ->and($object = new TestedClass(array($directory), $validator))
            ->then
                ->array($object->parse($file))->isIdenticalTo(array('phpswitch' => array('version' => '0.1.1')))
        ;
    }
}
