<?php
namespace tests\units\jubianchi\PhpSwitch\Configuration;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\fs\file;
use jubianchi\PhpSwitch\Configuration\Dumper as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Dumper extends atoum\test
{
    public function testDump() {
        $this
            ->if($directory = stream::get('directory'))
            ->if($file = file::getSubStream($directory, $name = uniqid()))
            ->and($file->isWritable(true))
            ->and($directory->readdir[1] = $file)
            ->and($object = new TestedClass())
            ->then
                ->object($object->dump((string) $file, array()))
                ->adapter($file)
                    ->call('stream_write')->withArguments("{  }")->once()
            ->if($values = array(
                $key = uniqid() => $value = uniqid(),
                $otherKey = uniqid() => array(
                    $subKey = uniqid() => $subValue = uniqid()
                )
            ))
            ->then
                ->object($object->dump((string) $file, $values))
                ->adapter($file)
                    ->call('stream_write')->withArguments("$key: $value\n$otherKey:\n  $subKey: $subValue\n")->once()
        ;
    }
}
