<?php
namespace tests\units\jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\PHP\Version;
use mageekguy\atoum;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\mock\streams\fs\file;
use jubianchi\PhpSwitch\PHP\Downloader as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Downloader extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf('\\jubianchi\\PhpSwitch\\Event\\Emitter')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($directory = uniqid())
            ->then
                ->object(new TestedClass($directory))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Downloader')
        ;
    }

	public function testGetDestinationHandle()
	{
		$this
			->if($dispatcher = new \mock\jubianchi\PhpSwitch\Event\Dispatcher())
			->and($destination = file::get())
			->and($destination->isNotWritable())
			->and($downloader = new \mock\jubianchi\PhpSwitch\PHP\Downloader(uniqid(), $dispatcher))
			->and($this->calling($downloader)->getDestination = (string) $destination)
			->then
				->exception(function() use ($downloader) {
					$downloader->getDestinationHandle(new Version(phpversion()));
				})
					->isInstanceOf('\\RuntimeException')
					->hasMessage('Could not write to ' . $destination)
			->if($destination->isWritable())
			->then
				->variable($destination)->isNotNull()
		;
	}
}
