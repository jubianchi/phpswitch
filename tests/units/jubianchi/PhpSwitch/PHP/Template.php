<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use mageekguy\atoum;
use jubianchi\PhpSwitch\PHP\Template as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Template extends atoum\test
{
    public function test__construct()
    {
        $this
			->if($template = new TestedClass($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion())))
			->then
				->object($template->getVersion())->isIdenticalTo($version)
				->array($template->getConfigs())->isEmpty()
				->variable($template->getOptions())->isNull()
		;
    }
}
