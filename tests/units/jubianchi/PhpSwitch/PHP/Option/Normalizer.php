<?php
namespace tests\units\jubianchi\PhpSwitch\PHP\Option;

use mageekguy\atoum;
use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\PHP\Option\OptionCollection;
use jubianchi\PhpSwitch\PHP\Option\Normalizer as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class Normalizer extends atoum\test
{
    public function testNormalize()
    {
        $this
            ->if($object = new TestedClass(new OptionCollection()))
            ->then
                ->string($object->normalize(new OptionCollection()))->isEmpty()
            ->if($option = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($option)->getAlias = '--option')
			->and($this->calling($option)->getName = 'option')
            ->and($otherOption = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($otherOption)->getAlias = '--otherOption')
			->and($this->calling($otherOption)->getName = 'otherOption')
			->and($collection = new OptionCollection(array($option, $otherOption)))
            ->then
                ->string($object->normalize($collection))->isEqualTo('--option --otherOption')
        ;
    }

    public function testDenormalize()
    {
        $this
            ->if($option = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($option)->getAlias = '--option')
            ->and($this->calling($option)->getMode = InputOption::VALUE_OPTIONAL)
            ->and($otherOption = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($otherOption)->getAlias = '--otherOption')
			->and($collection = new OptionCollection())
			->and($collection->addOptions(array($option, $otherOption)))
			->and($object = new TestedClass($collection))
            ->then
                ->object($object->denormalize(''))->isEmpty()
                ->object($object->denormalize('--option'))->isEqualTo(new OptionCollection(array($option)))
				->object($object->denormalize('--option --unknown'))->isEqualTo(new OptionCollection(array($option)))
                ->object($object->denormalize('--option=value'))->isEqualTo(new OptionCollection(array($option)))
                ->string($option->getValue())->isEqualTo('value')
        ;
    }
}
