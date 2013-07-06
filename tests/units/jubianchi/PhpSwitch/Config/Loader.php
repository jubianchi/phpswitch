<?php
namespace tests\units\jubianchi\PhpSwitch\Config;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\fs\file;
use jubianchi\PhpSwitch\Config\Loader as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Loader extends atoum\test
{
    public function testLoad()
    {
        $this
            ->if($directory = stream::get('directory'))
            ->and($file = file::getSubStream($directory, $name = uniqid()))
            ->and($validator = new \mock\jubianchi\PhpSwitch\Config\Validator())
            ->and($validator->getMockController()->validate = function($values) { return $values; })
            ->and($dumper = new \mock\jubianchi\PhpSwitch\Config\Dumper())
            ->and($dumper->getMockController()->dump = $dumper)
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader($name, $validator))
            ->then
                ->object($configuration = $object->load($directory, $dumper))->isInstanceOf('\\jubianchi\\PhpSwitch\\Config\\Configuration')
            ->if($file->setContents("phpswitch:\n    version: php-5.5.5\n"))
            ->then
                ->object($otherConfiguration = $object->load($directory, $dumper))
                    ->isInstanceOf('\\jubianchi\\PhpSwitch\\Config\\Configuration')
                    ->IsNotIdenticalTo($configuration)
            ->if($otherDirectory = stream::get('otherDirectory'))
            ->and($otherFile = file::getSubStream($otherDirectory, $name))
            ->and($otherFile->setContents("phpswitch:\n    version: php-5.4.4\n    mirror: foobar\n"))
            ->and($otherFile->setContents("phpswitch:\n    version: php-5.4.4\n    mirror: foobar\n"))
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader($name, $validator))
            ->then
                ->object($object->load(array($directory, $otherDirectory), $dumper))->isInstanceOf('\\jubianchi\\PhpSwitch\\Config\\Configuration')
            ->if($subDirectory = stream::getSubStream($otherDirectory, 'subDirectory'))
            ->and($thirdFile = file::getSubStream($subDirectory, $name))
            ->and($thirdFile->setContents("phpswitch:\n    version: php-5.4.5\n"))
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader($name, $validator))
            ->then
                ->object($object->load($subDirectory, $dumper, true))->isInstanceOf('\\jubianchi\\PhpSwitch\\Config\\Configuration')
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
