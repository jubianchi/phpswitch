<?php

use
	mageekguy\atoum\scripts\treemap,
	mageekguy\atoum\scripts\treemap\analyzers,
	mageekguy\atoum\scripts\treemap\categorizer
;

$testsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;

$featuresCategorizer = new categorizer('Features');
$featuresCategorizer
	->setMinDepthColor('#FFDD99')
	->setMaxDepthColor('#FFAA00')
	->setCallback(function($file) use ($testsDirectory) { return (substr($file->getFilename(), -8) == '.feature' && strpos($file->getRealpath(), $testsDirectory) === 0); })
;

$testsCategorizer = new categorizer('Tests');
$testsCategorizer
	->setMinDepthColor('#aae6ff')
	->setMaxDepthColor('#000f50')
	->setCallback(function($file) use ($testsDirectory) { return (substr($file->getFilename(), -4) == '.php' && strpos($file->getRealpath(), $testsDirectory) === 0); })
;

$phpCategorizer = new categorizer('Code');
$phpCategorizer
	->setMinDepthColor('#ffaac6')
	->setMaxDepthColor('#50001b')
	->setCallback(function($file) { return (substr($file->getFilename(), -4) == '.php'); })
;

$script
	->setProjectName(basename(__DIR__))
	->addDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'src')
	->addDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
	->setOutputDirectory(__DIR__ . '/doc/treemap')
	->addCategorizer($testsCategorizer)
	->addCategorizer($phpCategorizer)
	->addCategorizer($featuresCategorizer)
	->addAnalyzer(new analyzers\token())
	->addAnalyzer(new analyzers\size())
	->addAnalyzer(new analyzers\sloc())
	->addAnalyzer(new \mageekguy\atoum\scripts\treemap\analyzer\generic(
		'commit',
		'Commits',
		function(\splFileInfo $file)
		{
			$commit = exec('git log --pretty=oneline ' . escapeshellarg($file->getRealpath()) . ' | wc -l');

			return (int) trim($commit);
		}
	))
;