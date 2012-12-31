<?php
namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Console\Command\Finder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class PhpSwitch
{
    /** @var \Pimple */
    private $container;

    public static function init()
    {
        return new static(static::getEnv());
    }

    protected static function getEnv()
    {
        $env = array();
        $map = array(
            'PHPSWITCH_PREFIX' => 'app.workspace.path',
            'PHPSWITCH_HOME' => 'app.user.path',
            'PHPSWITCH_CONFIG' => 'app.config.name',
        );

        array_walk(
            $_SERVER,
            function($value, $key) use (& $env, $map) {
                if (preg_match('/^PHPSWITCH_/', $key) && array_key_exists($key, $map)) {
                    $env[$map[$key]] = $value;
                }
            }
        );

        return $env;
    }

    protected function __construct(array $env = array())
    {
        $this->container = new \Pimple();

        $this
            ->initEnv($env)
            ->initApplication()
            ->initConfiguration()
            ->initPhp()
        ;
    }

    public function run()
    {
        $this->container['app']->run();
    }

    protected function initEnv(array $env = array())
    {
        $this->container['app.path'] = realpath(__DIR__ . '/../../../');
        $this->container['app.source.path'] = $this->container['app.path'] . DIRECTORY_SEPARATOR . 'src';
        $this->container['app.command.path'] = $this->container['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Console/Command';
        $this->container['app.templates.path'] = $this->container['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Templates';
        $this->container['app.php.option.path'] = $this->container['app.source.path'] . DIRECTORY_SEPARATOR . '/jubianchi/PhpSwitch/PHP/Option';
        $this->container['app.user.path'] = getenv('HOME');
        $this->container['app.workspace.path'] = $this->container['app.path'] . DIRECTORY_SEPARATOR . '.phpswitch';
        $this->container['app.config.name'] = '.phpswitch.yml';
        $this->container['app.logger.output.path'] = 'php://stdout';
        $this->container['app.logger.error.path'] = 'php://stderr';

        foreach ($env as $key => $value) {
            $this->container[$key] = $value;
        }

        $this->container['app.workspace.downloads.path'] = $this->container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'downloads';
        $this->container['app.workspace.sources.path'] = $this->container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'sources';
        $this->container['app.workspace.installed.path'] = $this->container['app.workspace.path'] . DIRECTORY_SEPARATOR . 'installed';

        return $this;
    }

    protected function initApplication()
    {
        $this->container['app'] = function(\Pimple $container) {
            $appliction = new Console\Application();

            return $container['app.loader']
                ->load($appliction->setContainer($container))
                ->setConfiguration($container['app.config'])
            ;
        };

        $this->container['app.loader'] = function(\Pimple $container) {
            return new Console\Loader($container['app.command.finder']);
        };

        $this->container['app.command.finder'] = function(\Pimple $container) {
            return new Finder($container['app.command.path'], $container['app.source.path']);
        };

        $this->container['app.logger'] = function(\Pimple $container) {
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

        $this->container['app.twig'] = function(\Pimple $container) {
            $loader = new \Twig_Loader_Filesystem($container['app.templates.path']);
            return new \Twig_Environment($loader);
        };

        return $this;
    }

    protected function initConfiguration()
    {
        $this->container['app.config'] = function(\Pimple $container) {
            return $container['app.config.loader']->load(
                $container['app.config.name'],
                new Config\Configuration(),
                $container['app.config.dumper']
            );
        };

        $this->container['app.config.loader'] = function(\Pimple $container) {
            return new Config\Loader($container['app.user.path'], $container['app.config.validator']);
        };

        $this->container['app.config.validator'] = function(\Pimple $container) {
            return new Config\Validator($container['app.path']);
        };

        $this->container['app.config.dumper'] = function(\Pimple $container) {
            return new Config\Dumper($container['app.user.path']);
        };

        return $this;
    }

    protected function initPhp()
    {
        $this->container['app.php.builder'] = function(\Pimple $container) {
            return new PHP\Builder($container['app.workspace.installed.path']);
        };

        $this->container['app.php.extracter'] = function(\Pimple $container) {
            return new PHP\Extracter($container['app.workspace.sources.path']);
        };

        $this->container['app.php.downloader'] = function(\Pimple $container) {
            return new PHP\Downloader($container['app.workspace.downloads.path']);
        };

        $this->container['app.php.option.finder'] = function(\Pimple $container) {
            return new PHP\Option\Finder(
                $container['app.php.option.path'],
                $container['app.source.path']
            );
        };

        return $this;
    }
}
