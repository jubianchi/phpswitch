<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use mageekguy\atoum;
use jubianchi\PhpSwitch\PHP\Template as TestedClass;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\PHP\Option\OptionCollection;

require_once __DIR__ . '/../../../bootstrap.php';

class Template extends atoum\test
{
    public function test__construct()
    {
        $this
			->if($template = new TestedClass($version = new Version(phpversion())))
			->then
				->object($template->getVersion())->isIdenticalTo($version)
				->array($template->getConfigs())->isEmpty()
				->variable($template->getOptions())->isNull()
		;
    }

	public function testGetName()
	{
		$this
			->if($template = new TestedClass($version = new Version(phpversion())))
			->then
				->string($template->getName())->isEqualTo(str_replace('.', '-', $version))
		;
	}

	public function testGetSetOptions()
	{
		$this
			->if($template = new TestedClass($version = new Version(phpversion())))
			->then
				->object($template->setOptions($collection = new OptionCollection()))->isIdenticalTo($template)
				->object($template->getOptions())->isIdenticalTo($collection)
		;
	}

	public function testGetSetConfigs()
	{
		$this
			->if($template = new TestedClass($version = new Version(phpversion())))
			->then
				->object($template->setConfigs($configs = array(uniqid() => uniqid())))->isIdenticalTo($template)
				->array($template->getConfigs())->isIdenticalTo($configs)
		;
	}
}
