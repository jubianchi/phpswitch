<?php
namespace tests\units\jubianchi\PhpSwitch\Config;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use jubianchi\PhpSwitch\Config\Loader as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Loader extends atoum\test
{
    public function testLoad()
    {
        $this
            ->if($directory = stream::get('directory'))
            ->and($file = stream::getSubStream($directory, $name = uniqid()))
            ->and($file->file_put_contents = true)
            ->and($directory->readdir[1] = $file)
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->and($configuration->getMockController()->getValues = array())
            ->and($validator = new \mock\jubianchi\PhpSwitch\Config\Validator())
            ->and($dumper = new \mock\jubianchi\PhpSwitch\Config\Dumper($directory))
            ->and($dumper->getMockController()->dump = $dumper)
            ->and($object = new \mock\jubianchi\PhpSwitch\Config\Loader($directory, $validator))
            ->and($object->getMockController()->parse = array())
            ->then
                ->object($object->load($name, $configuration, $dumper))->isIdenticalTo($configuration)
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
            ->and($object = new TestedClass($directory, $validator))
            ->then
                ->array($object->parse($file))->isIdenticalTo(array('phpswitch' => array('version' => '0.1.1')))
        ;
    }
}
