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
    ->addAnalyzer(new \mageekguy\atoum\scripts\treemap\analyzer\generic(
        'decision',
        'Decisions',
        function(\splFileInfo $file)
        {
            $tokenFilter = function($token) {
                if (is_array($token))
                {
                    switch ($token[0])
                    {
                        case T_IF:
                        case T_ELSE:
                        case T_ELSEIF:
                        case T_FOR:
                        case T_FOREACH:
                        case T_WHILE:
                        case T_CASE:
                        case T_DEFAULT:
                            return true;

                        default:
                            return false;
                    }
                }

                return false;
            };

            return sizeof(array_filter(token_get_all(file_get_contents((string) $file)), $tokenFilter));
        }
    ))
    ->addAnalyzer(new \mageekguy\atoum\scripts\treemap\analyzer\generic(
        'methods',
        'Methods',
        function(\splFileInfo $file)
        {
            $methods = 0;
            $current = null;
            $tokens = token_get_all(file_get_contents((string) $file));

            for ($i = 0; $i < sizeof($tokens); $i++)
            {
                $token = $tokens[$i];
                $current = is_array($token) ? $token[0] : $token;

                if(in_array($current, array(T_PUBLIC, T_PROTECTED, T_PRIVATE)))
                {
                    do {
                        $next = $tokens[++$i];
                        $next = is_array($next) ? $next[0] : $next;
                    } while(in_array($next, array(' ', T_WHITESPACE)));

                    if ($next === T_FUNCTION)
                    {
                        $methods++;
                    }
                }
            }

            return $methods;
        }
    ))
    ->addAnalyzer(new \mageekguy\atoum\scripts\treemap\analyzer\generic(
        'complexity',
        'Complexity',
        function(\splFileInfo $file)
        {
            $tokenFilter = function($token) {
                if (is_array($token))
                {
                    switch ($token[0])
                    {
                        case T_IF:
                        case T_ELSE:
                        case T_ELSEIF:
                        case T_FOR:
                        case T_FOREACH:
                        case T_WHILE:
                        case T_CASE:
                        case T_DEFAULT:
                            return true;

                        default:
                            return false;
                    }
                }

                return false;
            };

            $methodFilter = function(\splFileInfo $file)
            {
                $methods = 0;
                $current = null;
                $tokens = token_get_all(file_get_contents((string) $file));

                for ($i = 0; $i < sizeof($tokens); $i++)
                {
                    $token = $tokens[$i];
                    $current = is_array($token) ? $token[0] : $token;

                    if(in_array($current, array(T_PUBLIC, T_PROTECTED, T_PRIVATE)))
                    {
                        do {
                            $next = $tokens[++$i];
                            $next = is_array($next) ? $next[0] : $next;
                        } while(in_array($next, array(' ', T_WHITESPACE)));

                        if ($next === T_FUNCTION)
                        {
                            $methods++;
                        }
                    }
                }

                return $methods;
            };

            $decisions = sizeof(array_filter(token_get_all(file_get_contents((string) $file)), $tokenFilter));
            $methods = $methodFilter($file);

            return ($decisions - $methods) < 0 ? 0 : ($decisions - $methods);
        }
    ))
;