<?php
use mageekguy\atoum;
use mageekguy\atoum\report\fields\runner;

define('TESTS_DIRECTORY', __DIR__ . '/tests/units');

$runner->setBootstrapFile(__DIR__ . '/tests/units/bootstrap.php');
$runner->addTestsFromDirectory(TESTS_DIRECTORY);

$script->noCodeCoverageForNamespaces('Symfony');
