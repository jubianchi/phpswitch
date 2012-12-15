<?php
namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Console\Command\Finder;

require_once __DIR__ . '/../../../vendor/autoload.php';

define(__NAMESPACE__ . '\\PHPSWITCH_HOME', realpath(__DIR__ . '/../../../'));
define(__NAMESPACE__ . '\\PHPSWITCH_SRC', PHPSWITCH_HOME . '/src');
define(__NAMESPACE__ . '\\HOME',  getenv('HOME'));

$app = new Console\Application();

$appConfigLoader = new Config\Loader(HOME, new Config\Validator(HOME));
$appConfigDumper = new Config\Dumper(HOME);

$appConfig = new Config\Configuration();
$appConfigLoader->load(
    '.phpswitch.yml',
    $appConfig,
    $appConfigDumper
);

$appLoader = new Console\Loader(new Finder(__DIR__ . '/Console/Command', PHPSWITCH_SRC));
$appLoader->load($app);

return $app->setConfiguration($appConfig)->run();
