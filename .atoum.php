<?php
use \mageekguy\atoum;

function colorized() {
    $color = -1;
    if(false !== ($term = getenv('TERM'))) {
        if(preg_match('/\d+/', $term, $matches) > 0) {
            $color = $matches[0];
        }
    }

    if($color < 0) {
        $color = system('tput colors');
    }

    return ($color >= 256);
}

define('COVERAGE_TITLE', 'phpswitch');
define('COVERAGE_DIRECTORY', __DIR__ . '/coverage');
define('COVERAGE_WEB_PATH', 'file://' . COVERAGE_DIRECTORY);
define('COLORIZED', colorized());

if(false === is_dir(COVERAGE_DIRECTORY))
{
    mkdir(COVERAGE_DIRECTORY, 0777, true);
}

$stdOutWriter = new atoum\writers\std\out();
$cliReport = new atoum\reports\realtime\cli();
$cliReport->addWriter($stdOutWriter);

$coverageField = new atoum\report\fields\runner\coverage\html(COVERAGE_TITLE, COVERAGE_DIRECTORY);
$coverageField->setRootUrl(COVERAGE_WEB_PATH);
$cliReport->addField($coverageField, array(atoum\runner::runStop));

if(COLORIZED)
{
    $cliReport->addField(new atoum\report\fields\runner\atoum\logo());
    $cliReport->addField(new atoum\report\fields\runner\result\logo());
}

$runner->addReport($cliReport);
$runner->setBootstrapFile(__DIR__ . '/tests/bootstrap.php');
$script->addTestAllDirectory(__DIR__ . '/tests');
