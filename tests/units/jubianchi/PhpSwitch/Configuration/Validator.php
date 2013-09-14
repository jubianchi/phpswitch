<?php
namespace tests\units\jubianchi\PhpSwitch\Configuration;

use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mock\jubianchi\PhpSwitch\Configuration\Validator as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Validator extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubclassOf('\\Symfony\\Component\\Config\\Definition\\ConfigurationInterface')
        ;
    }

    public function testValidate()
    {
        $this
            ->if($object = new TestedClass())
            ->and($processor = new \mock\Symfony\Component\Config\Definition\Processor())
            ->and($this->calling($processor)->processConfiguration = true)
            ->then
                ->boolean($object->validate(array(), $processor))->isTrue()
                ->mock($processor)
                    ->call('processConfiguration')->withArguments($object, array())->once()
        ;
    }
}
