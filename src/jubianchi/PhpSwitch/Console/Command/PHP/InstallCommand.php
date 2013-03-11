<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

class InstallCommand extends Command
{
    const NAME = 'php:install';
    const DESC = 'Installs a PHP version';

    /** @var \jubianchi\PhpSwitch\PHP\Option\Option[] */
    private $options = array();

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('version', InputArgument::REQUIRED, 'PHP version (x.y.z)')
            ->addOption('alias', 'a', InputOption::VALUE_REQUIRED, 'Version name alias')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Use the same configuration as an existing version')
            ->addOption('jobs', 'j', InputOption::VALUE_REQUIRED, 'Number of jobs to run simultaneously')
            ->addOption('ini', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'INI configuration directives')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Application $application
     */
    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);

        if (null !== $application) {
            foreach ($application->getOptionFinder() as $option) {
                $option = new $option();
                $option->setCommand($this);
                $this->options[] = $option;
            }
        }
    }

    protected function getSubscriber(OutputInterface $output)
    {
        $self = $this;
        $subscriber = new \jubianchi\PhpSwitch\Event\Subscriber();
        $indent = self::INDENT;

        $afterCallback = function() use ($output) { $output->write(PHP_EOL); };
        $processCallback = function($event) use ($self, $output) {
            $self->log($event['buffer'], 'err' === $event['type'] ? \Monolog\Logger::ERROR : \Monolog\Logger::INFO);
            $self->getHelper('progress')->advance();
        };

        $subscriber
            ->handle('install.before', function($event) use ($output) {
                $output->writeln(
                    array(
                        sprintf('Installing PHP <info>%s</info>', $event['version']->getVersion()),
                        sprintf('From mirror <info>%s</info>', $event['mirror']),
                        sprintf('Configure options: <info>[%s]</info>', $event['options'])
                    )
                );
            })
            ->handle('install.after', function($event) use ($output, $indent) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'PHP version <info>%s</info> was installed:', $event['version']),
                    sprintf('%s<comment>%s</comment>', $indent, $event['prefix'])
                ));
            })
            ->handle('download.before', function($event) use ($self, $output, $indent) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Downloading PHP <info>%s</info>', $event['version']->getVersion()),
                    sprintf('%s<comment>%s</comment>', $indent, sprintf($event['version']->getUrl(), $event['mirror']))
                ));

                if (OutputInterface::VERBOSITY_VERBOSE !== $output->getVerbosity()) {
                    $self->startProgress($output, 100, '[%bar%] %percent%%');
                }
            })
            ->handle('download.progress', function($size, $downloaded) use ($self) {
                static $previous = 0;

                if ($size > 0) {
                    $complete = ceil(($downloaded / $size) * 100);

                    $self->getHelper('progress')->advance($complete - $previous);

                    $previous = $complete;
                }
            })
            ->handle('download.after', $afterCallback)
            ->handle('extract.before', function($event) use ($self, $output, $indent) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Extracting <info>%s</info>', $event['version']->getVersion()),
                    sprintf('%s<comment>%s</comment>', $indent, $event['archive'])
                ));

                $self->startProgress($output);
            })
            ->handle('extract.progress', $processCallback)
            ->handle('extract.after', $afterCallback)
            ->handle('build.before', function($event) use ($self, $output, $indent) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Building <info>%s</info>', $event['version']->getVersion()),
                    sprintf('%s<comment>%s</comment>', $indent, $event['prefix'])
                ));

                $self->startProgress($output);
            })
            ->handle('build.progress', $processCallback)
            ->handle('build.after', $afterCallback)
        ;

        return $subscriber;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $finder = $this->getApplication()->getService('app.php.finder');
        $version = $finder->getVersion($input->getArgument('version'));
        $mirror = $this->getConfiguration()->get('mirror');
        if (null !== ($alias = $input->getOption('alias'))) {
            $version->setName($alias);
        }
        $options = $this->resolveOptions($input);

        $this->getApplication()->getService('app.event.dispatcher')->addEventSubscriber($this->getSubscriber($output));
        $this->getInstaller()
            ->setOptions($options)
            ->install($version, $mirror, $input->getOption('jobs'), $input, $output)
        ;

        foreach ($input->getOption('ini') as $ini) {
            if (false !== ($ini = parse_ini_string($ini))) {
                $this->getApplication()->getService('app.php.config')->setValue($version, key($ini), current($ini));
            }
        }

        $this->getConfiguration()
            ->set('versions.' . str_replace('.', '-', $version), $options->normalize())
            ->dump()
        ;

        return 0;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Installer
     */
    public function getInstaller()
    {
        return $this->getApplication()->getService('app.php.installer');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Resolver
     */
    public function getResolver()
    {
        return $this->getApplication()->getOptionResolver();
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Normalizer
     */
    public function getNormalizer()
    {
        return $this->getApplication()->getOptionNormalizer();
    }

    protected function resolveOptions(InputInterface $input)
    {
        $options = $this->getResolver()->resolve($input, $this->options);

        if (null !== ($config = $input->getOption('config'))) {
            try {
                $config = $this->getConfiguration()->get('versions.' . str_replace('.', '-', $config));
            } catch (\InvalidArgumentException $exception) {
                throw new \InvalidArgumentException(
                    sprintf('Configuration %s does not exist', $config),
                    $exception->getCode(),
                    $exception
                );
            }

            $options->addOptions($this->getNormalizer()->denormalize($config, $this->options));
        }

        return $options;
    }

    public function startProgress(OutputInterface $output, $max = null, $format = '[%bar%]')
    {
        $progress = $this->getHelper('progress');

        $progress->setBarWidth(50);
        $progress->setEmptyBarCharacter($max ? '-' : '=');
        $progress->setProgressCharacter('>');
        $progress->setFormat($format);

        $progress->start($output, $max);
    }
}
