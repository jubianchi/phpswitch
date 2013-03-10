<?php
use
    mageekguy\atoum,
    mageekguy\atoum\report\fields\runner
;

define('TESTALL_DIRECTORY', __DIR__ . '/tests/units');

define('COVERAGE_TITLE', basename(__DIR__));
define('COVERAGE_DIRECTORY', sys_get_temp_dir() . DIRECTORY_SEPARATOR . COVERAGE_TITLE . '_coverage');
define('COVERAGE_WEB_PATH', 'file://' . COVERAGE_DIRECTORY);

define('NOTIFIER_CLASS', '\\mageekguy\\atoum\\report\\fields\\runner\\result\\notifier\\image\\growl');
define('NOTIFIER_IMG_PATH', __DIR__ . '/vendor/atoum/atoum/resources/images/logo');

function colorized() {
    $color = -1;
    if (false !== ($term = getenv('TERM'))) {
        if (preg_match('/\d+/', $term, $matches) > 0) {
            $color = $matches[0];
        }
    }

    if ($color < 0) {
        $color = system('tput colors');
    }

    return ($color >= 256);
}

if (false === is_dir(COVERAGE_DIRECTORY)) {
    mkdir(COVERAGE_DIRECTORY, 0777, true);
}

$coverage = new runner\coverage\html(COVERAGE_TITLE, COVERAGE_DIRECTORY);
$coverage->setRootUrl(COVERAGE_WEB_PATH);

$notifier = null;
if (class_exists(NOTIFIER_CLASS))
{
    $class = NOTIFIER_CLASS;
    $notifier = new $class();

    if($notifier instanceof runner\result\notifier\image) {
        $notifier
            ->setSuccessImage(NOTIFIER_IMG_PATH . DIRECTORY_SEPARATOR . 'success.png')
            ->setFailureImage(NOTIFIER_IMG_PATH . DIRECTORY_SEPARATOR . 'failure.png')
        ;
    }
}

$report = $script->AddDefaultReport();
$report->addField($coverage, array(atoum\runner::runStop));

if (null !== $notifier) {
    $report->addField($notifier);
}

if(colorized())
{
    $report->addField(new runner\atoum\logo());
    $report->addField(new runner\result\logo());
}

$script->addTestAllDirectory(TESTALL_DIRECTORY);
