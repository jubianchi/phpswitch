<?php
namespace jubianchi\PhpSwitch;

use jubianchi\PhpSwitch\Console\Command\Finder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use jubianchi\PhpSwitch\Phar\Runnable;

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
            ->initApplication()
            ->initConfiguration()
            ->initPhp()
        ;
    }

    public function run()
    {
        $this->container['app']->run();
    }

    protected function initEnv($path, array $env = array())
    {
        $this->container['parameters']['app.path'] = $path;
        $this->container['parameters']['app.source.path'] = $this->container['parameters']['app.path'] . DIRECTORY_SEPARATOR . 'src';
        $this->container['parameters']['app.command.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Console/Command';
        $this->container['parameters']['app.templates.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . 'jubianchi/PhpSwitch/Templates';
        $this->container['parameters']['app.php.option.path'] = $this->container['parameters']['app.source.path'] . DIRECTORY_SEPARATOR . '/jubianchi/PhpSwitch/PHP/Option';
        $this->container['parameters']['app.user.path'] = getenv('HOME');
        $this->container['parameters']['app.workspace.path'] = $this->container['parameters']['app.path'] . DIRECTORY_SEPARATOR . '.phpswitch';
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

        return $this;
    }

    protected function initApplication()
    {
        $this->container['app'] = $this->container->share(
            function(\Pimple $container) {
                $application = new Console\Application();

                return $container['app.loader']
                    ->load($application->setContainer($container))
                    ->setConfiguration($container['app.config'])
                ;
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
            function() {
                return new Process\Builder();
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
        $this->container['app.config'] = $this->container->share(
            function(\Pimple $container) {
                return $container['app.config.loader']->load(
                    $container['parameters']['app.config.name'],
                    new Config\Configuration(),
                    $container['app.config.dumper']
                );
            }
        );

        $this->container['app.config.loader'] = $this->container->share(
            function(\Pimple $container) {
                return new Config\Loader(
                    array(
                        $container['parameters']['app.user.path'],
                        getcwd() => Config\Loader::DIRECTORY_BUBBLE,
                    ),
                    $container['app.config.validator']
                );
            }
        );

        $this->container['app.config.validator'] = $this->container->share(
            function(\Pimple $container) {
                return new Config\Validator($container['parameters']['app.path']);
            }
        );

        $this->container['app.config.dumper'] = $this->container->share(
            function(\Pimple $container) {
                return new Config\Dumper(
                    array(
                        Config\Dumper::GLOBAL_DIR => $container['parameters']['app.user.path'],
                        Config\Dumper::LOCAL_DIR => getcwd(),
                    )
                );
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
					$option = new $option();
					$options->addOption($option);
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
            function() {
                return new PHP\Finder(
                    array(
                        'http://php.net/releases' => '/(PHP\s*([4-5]\.(?:\d+\.?)*) \(tar\.bz2\))/',
                        'http://php.net/downloads.php' => '/(PHP\s*([4-5]\.(?:\d+\.?)*) \(tar\.bz2\))/',
                        'http://snaps.php.net/' => '/(php\-(?:([4-5]\.(?:\d+\.?)*\-dev)|\-trunk) \(tar\.bz2\))/',
                        'http://downloads.php.net/stas' => '/(php-([4-5]\.(?:\d+\.?)*)\.tar\.bz2)/',
                        'http://downloads.php.net/dsp' => '/(php-([4-5]\.(?:\d+\.?)*(?:alpha\d*)?)\.tar\.bz2)/'
                    )
                );
            }
        );

		$this->container['app.php.template.builder'] = $this->container->share(
			function(\Pimple $container) {
				return new \jubianchi\PhpSwitch\Console\Template\Builder(
					$container['app.php.option.resolver'],
					$container['app.php.option.normalizer'],
					$container['app.config']
				);
			}
		);

        return $this;
    }
}
