<?php
namespace tests\units\jubianchi\PhpSwitch\Config;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use jubianchi\PhpSwitch\Config\Dumper as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Dumper extends atoum\test
{
    public function testDump() {
        $this
            ->if($directory = stream::get('directory'))
            ->if($file = stream::getSubStream($directory, $name = uniqid()))
            ->and($file->file_get_contents = '')
            ->and($file->file_put_contents = true)
            ->and($directory->readdir[1] = $file)
            ->and($object = new TestedClass($directory))
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Config\Configuration())
            ->then
                ->object($object->dump($name, $configuration))
                ->adapter($file)
                    ->call('stream_write')->withArguments("phpswitch: {  }\n")->once()
            ->if($values = array(
                $key = uniqid() => $value = uniqid(),
                $otherKey = uniqid() => array(
                    $subKey = uniqid() => $subValue = uniqid()
                )
            ))
            ->and($configuration->setValues($values))
            ->then
                ->object($object->dump($name, $configuration))
                ->adapter($file)
                    ->call('stream_write')->withArguments("phpswitch:\n    $key: $value\n    $otherKey: { $subKey: $subValue }\n")->once()
        ;
    }
}