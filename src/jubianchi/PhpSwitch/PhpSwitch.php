<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Configuration;
use jubianchi\PhpSwitch\Console\Application\Configuration as AppConfiguration;
use jubianchi\PhpSwitch\Console\Command\Finder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use jubianchi\PhpSwitch\Phar\Runnable;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class PhpSwitch implements Runnable
{
    protected $container;
    protected $env = array();

    protected static function getEnv($env = array())
    {
        $map = array(
            'PHPSWITCH_PREFIX' => 'app.workspace.path',
            'PHPSWITCH_HOME' => 'app.user.path',
            'PHPSWITCH_CONFIG' => 'app.config.name',
        );

        foreach ($map as $var => $key) {
            if (false !== ($value = getenv($var))) {
                $env[$key] = $value;
            }
        }

        return $env;
    }

    public function __construct($path, array $env = array())
    {
        $this->container = new \Pimple();
        $this->container['parameters'] = new \Pimple();
        $this->env = $env;

        $this
            ->initEnv($path, static::getEnv($env))
            ->initConfiguration()
            ->initPhp()
            ->initApplication()
        ;
    }

    public function run()
    {
        $this->container['app']->run($this->container['app.input'], $this->container['app.output']);
    }

    protected function initEnv($path, array $env = array())
    {
        $this->container['parameters']['app.path'] = $path;
        $this->container['parameters']['app.source.path'] = $this->container['parameters']['app.path'] . DIRECTORY_SEPARATOR . 'src';
        $this->container['parameters']['app.command.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Console/Command';
        $this->container['parameters']['app.templates.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Templates';
        $this->container['parameters']['app.php.option.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . '/jubianchi/PhpSwitch/PHP/Option';
        $this->container['parameters']['app.user.path'] = getenv('HOME');
        $this->container['parameters']['app.workspace.path'] = $this->container['parameters']['app.user.path'] . DIRECTORY_SEPARATOR . '.phpswitch';
        $this->container['parameters']['app.config.name'] = '.phpswitch.yml';
        $this->container['parameters']['app.logger.output.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'phpswitch.log';
        $this->container['parameters']['app.logger.error.path'] = $this->container['parameters']['app.logger.output.path'];

        foreach ($env as $key => $value) {
            $this->container['parameters'][$key] = $value;
        }

        $this->container['parameters']['app.workspace.path'] =
            false !== ($path = realpath($this->container['parameters']['app.workspace.path']))
                ? $path
                : $this->container['parameters']['app.workspace.path'];

        $this->container['parameters']['app.workspace.downloads.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'downloads';
        $this->container['parameters']['app.workspace.sources.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'sources';
        $this->container['parameters']['app.workspace.installed.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'installed';
        $this->container['parameters']['app.workspace.doc.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'doc';
        $this->container['parameters']['app.workspace.cache.path'] = $this->container['parameters']['app.workspace.path'] . DIRECTORY_SEPARATOR . 'phpswitch.cache';

        return $this;
    }

    protected function initApplication()
    {
        $this->container['app'] = $this->container->share(
            function(\Pimple $container) {
                $application = new Console\Application($container);

                return $container['app.loader']->load($application);
            }
        );

        $this->container['app.input'] = $this->container->share(
            function() {
                return new ArgvInput();
            }
        );

        $this->container['app.output'] = $this->container->share(
            function() {
                return new ConsoleOutput();
            }
        );

        $this->container['app.loader'] = $this->container->share(
            function(\Pimple $container) {
                return new Console\Loader($container['app.command.finder']);
            }
        );

        $this->container['app.command.finder'] = $this->container->share(
            function(\Pimple $container) {
                return new Finder(
                    $container['parameters']['app.command.path'],
                    $container['parameters']['app.source.path']
                );
            }
        );

        $this->container['app.logger'] = $this->container->share(
            function(\Pimple $container) {
                $logger = new Logger('output');
                $formatter = new \Monolog\Formatter\LineFormatter();

                $info = new StreamHandler($container['parameters']['app.logger.output.path'], Logger::INFO);
                $info->setFormatter($formatter);
                $logger->pushHandler($info);

                $error = new StreamHandler($container['parameters']['app.logger.error.path'], Logger::ERROR, false);
                $error->setFormatter($formatter);
                $logger->pushHandler($error);

                return $logger;
            }
        );

        $this->container['app.twig'] = $this->container->share(
            function(\Pimple $container) {
                $loader = new \Twig_Loader_Filesystem($container['parameters']['app.templates.path']);

                return new \Twig_Environment($loader);
            }
        );

        $this->container['app.process.builder'] = $this->container->share(
            function(\Pimple $container) {
                return new Process\Builder\Factory($container['app.process.askpass']);
            }
        );

        $this->container['app.process.askpass'] = $this->container->share(
            function(\Pimple $container) {
                return new Process\AskPass(new Process\Builder\Factory(), $container['app.output'], $container['app']->getHelperSet()->get('dialog'));
            }
        );

        $this->container['app.event.dispatcher'] = $this->container->share(
            function() {
                return new Event\Dispatcher();
            }
        );

        return $this;
    }

    protected function initConfiguration()
    {
        $this->container['app.config.dumper'] = $this->container->share(
            function(\Pimple $container) {
                return new Configuration\Dumper();
            }
        );

        $this->container['app.config.user'] = $this->container->share(
            function(\Pimple $container) {
                return new Configuration\Yaml(
                    $container['parameters']['app.user.path'] . DIRECTORY_SEPARATOR . $container['parameters']['app.config.name'],
                    $container['app.config.dumper'],
                    new Configuration\Validator\User()
                );
            }
        );

        $this->container['app.config.local'] = $this->container->share(
            function(\Pimple $container) {
                return new Configuration\Yaml(
                    getcwd(). DIRECTORY_SEPARATOR . $container['parameters']['app.config.name'],
                    $container['app.config.dumper'],
                    new Configuration\Validator\Local()
                );
            }
        );

        $this->container['app.config'] = $this->container->share(
            function(\Pimple $container) {
                $configuration = new Configuration\Collection();

                return $configuration
                    ->add($container['app.config.user'])
                    ->add($container['app.config.local'])
                ;
            }
        );

        return $this;
    }

    protected function initPhp()
    {
        $this->container['app.php.builder'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Builder(
                    $container['parameters']['app.workspace.installed.path'],
                    $container['app.process.builder'],
                    $container['app.event.dispatcher']
                );
            }
        );

        $this->container['app.php.extracter'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Extracter(
                    $container['parameters']['app.workspace.sources.path'],
                    $container['app.process.builder'],
                    $container['app.event.dispatcher']
                );
            }
        );

        $this->container['app.php.downloader'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Downloader(
                    $container['parameters']['app.workspace.downloads.path'],
                    $container['app.event.dispatcher']
                );
            }
        );

        $this->container['app.php.option.finder'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Option\Finder(
                    $container['parameters']['app.php.option.path'],
                    $container['parameters']['app.source.path']
                );
            }
        );

        $this->container['app.php.options'] = $this->container->share(
            function(\Pimple $container) {
                $options = new PHP\Option\OptionCollection();

                foreach ($container['app.php.option.finder'] as $option) {
                    $options->addOption(new $option());
                }

                return $options;
            }
        );

        $this->container['app.php.option.resolver'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Option\Resolver($container['app.php.options']);
            }
        );

        $this->container['app.php.option.normalizer'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Option\Normalizer($container['app.php.options']);
            }
        );

        $this->container['app.php.config'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Config(
                    $container['parameters']['app.workspace.installed.path']
                );
            }
        );

        $this->container['app.php.installer'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\Installer(
                    $container['app.php.downloader'],
                    $container['app.php.extracter'],
                    $container['app.php.builder'],
                    $container['app.event.dispatcher']
                );
            }
        );

        $this->container['app.php.finder'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\CachingFinder(
                    $container['parameters']['app.workspace.cache.path'],
                    array(
                        'http://php.net/releases' => '/(PHP\s*([4-5]\.(?:\d+\.?)*) \(tar\.bz2\))/',
                        'http://php.net/downloads.php' => '/(php-([4-5]\.(?:\d+\.?)*)\.tar\.bz2)/',
                        'http://snaps.php.net/' => '/(php\-(?:([4-5]\.(?:\d+\.?)*\-dev)|\-trunk) \(tar\.bz2\))/',
                        'http://downloads.php.net/stas' => '/(php-([4-5]\.(?:\d+\.?)*)\.tar\.bz2)/',
                        'http://downloads.php.net/dsp' => '/(php-([4-5]\.(?:\d+\.?)*(?:(?:alpha|beta)\d*)?)\.tar\.bz2)/'
                    )
                );
            }
        );

        $this->container['app.php.finder.cached'] = $this->container->share(
            function(\Pimple $container) {
                return new PHP\CachedFinder(
                    $container['app.php.finder'],
                    $container['parameters']['app.workspace.cache.path']
                );
            }
        );

        $this->container['app.php.template.builder'] = $this->container->share(
            function(\Pimple $container) {
                return new Console\Template\Builder(
                    $container['app.php.option.resolver'],
                    $container['app.php.option.normalizer'],
                    $container['app.config']
                );
            }
        );

        return $this;
    }
}
