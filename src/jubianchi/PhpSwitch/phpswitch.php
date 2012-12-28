<?php
namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Console\Command\Finder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/../../../vendor/autoload.php';

$container = new \Pimple();

$container['app.path'] = realpath(__DIR__ . '/../../../');
$container['app.source.path'] = $container['app.path'] . DIRECTORY_SEPARATOR . 'src';
$container['app.user.path'] = getenv('HOME');
$container['app.workspace.path'] = $container['app.path'] . DIRECTORY_SEPARATOR . '.phpswitch';
$container['app.workspace.downloads.path'] = $container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'downloads';
$container['app.workspace.sources.path'] = $container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'sources';
$container['app.workspace.installed.path'] = $container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'installed';
$container['app.command.path'] = $container['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Console/Command';
$container['app.php.option.path'] = $container['app.source.path'] . DIRECTORY_SEPARATOR . '/jubianchi/PhpSwitch/PHP/Option';
$container['app.config.name'] = '.phpswitch.yml';
$container['app.logger.output.path'] = 'php://stdout';
$container['app.logger.error.path'] = 'php://stderr';

$container['app'] = function(\Pimple $container) {
    $appliction = new Console\Application();
    return $container['app.loader']
        ->load($appliction->setContainer($container))
            ->setConfiguration($container['app.config'])
    ;
};

$container['app.loader'] = function(\Pimple $container) {
    return new Console\Loader($container['app.command.finder']);
};

$container['app.command.finder'] = function(\Pimple $container) {
    return new Finder($container['app.command.path'], $container['app.source.path']);
};

$container['app.config'] = function(\Pimple $container) {
    return $container['app.config.loader']->load(
        $container['app.config.name'],
        new Config\Configuration(),
        $container['app.config.dumper']
    );
};

$container['app.config.validator'] = function(\Pimple $container) {
    return new Config\Validator($container['app.path']);
};

$container['app.config.loader'] = function(\Pimple $container) {
    return new Config\Loader($container['app.user.path'], $container['app.config.validator']);
};

$container['app.config.dumper'] = function(\Pimple $container) {
    return new Config\Dumper($container['app.user.path']);
};

$container['app.php.builder'] = function(\Pimple $container) {
    return new PHP\Builder($container['app.workspace.installed.path']);
};

$container['app.php.extracter'] = function(\Pimple $container) {
    return new PHP\Extracter($container['app.workspace.sources.path']);
};

$container['app.php.downloader'] = function(\Pimple $container) {
    return new PHP\Downloader($container['app.workspace.downloads.path']);
};

$container['app.php.option.finder'] = function(\Pimple $container) {
    return new PHP\Option\Finder(
        $container['app.php.option.path'],
        $container['app.source.path']
    );
};

$container['app.logger'] = function(\Pimple $container) {
	$logger = new Logger('output');
	$formatter = new \Monolog\Formatter\LineFormatter('%message%');

	$info = new StreamHandler($container['app.logger.output.path'], Logger::INFO);
	$info->setFormatter($formatter);
	$logger->pushHandler($info);


	$error = new StreamHandler($container['app.logger.error.path'], Logger::ERROR, false);
	$error->setFormatter($formatter);
	$logger->pushHandler($error);

	return $logger;
};

return $container['app']->run();
