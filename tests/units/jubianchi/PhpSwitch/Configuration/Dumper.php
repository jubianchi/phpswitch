<?php
namespace tests\units\jubianchi\PhpSwitch\Configuration;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\fs\file;
use mock\jubianchi\PhpSwitch\Configuration;
use jubianchi\PhpSwitch\Configuration\Dumper as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Dumper extends atoum\test
{
    public function testDump() {
        $this
            ->given($directory = stream::get('directory'))
            ->and($file = file::getSubStream($directory, $name = uniqid()))
            ->and($file->isWritable(true))
            ->and($directory->readdir[1] = $file)
            ->and(
                $this->mockGenerator->orphanize('__construct')->shuntParentClassCalls(),
                $configuration = new Configuration()
            )
            ->and($object = new TestedClass())
            ->if($this->calling($configuration)->__toString = $config = uniqid())
            ->then
                ->object($object->dump((string) $file, $configuration))
                ->adapter($file)
                    ->call('stream_write')->withArguments($config)->once()
        ;
    }
}
