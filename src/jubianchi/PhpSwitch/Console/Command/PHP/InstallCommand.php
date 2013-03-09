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

    /** @var string */
    private $archive;

    /** @var string */
    private $source;

    /** @var string */
    private $prefix;

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

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $finder = $this->getApplication()->getService('app.php.finder');
        $version = $finder->getVersion($input->getArgument('version'));

        if (null !== ($alias = $input->getOption('alias'))) {
            $version->setName($alias);
        }

        $dest = $this->getBuilder()->getDestination($version);
        if (true === file_exists($dest)) {
            throw new \RuntimeException(sprintf('PHP version %s is already installed', $version));
        }

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

            $options = array_merge(
                $this->getNormalizer()->denormalize($config, $this->options),
                $options
            );
        }

        $options = array_unique($options);
        $normalized = $this->getNormalizer()->normalize($options);

        $output->writeln(
            array(
                sprintf('Installing PHP <info>%s</info>', $version->getVersion()),
                sprintf('Configure options: <info>[%s]</info>', $normalized)
            )
        );

        foreach ($options as $option) {
            $option->preInstall($version, $input, $output);
        }

        $this
            ->download($version, $output)
            ->extract($version, $output)
            ->install($version, $this->getNormalizer()->normalize($options), $input->getOption('jobs'), $output)
            ->getConfiguration()
                ->set('versions.' . str_replace('.', '-', $version), $normalized)
                ->dump()
        ;

        foreach ($options as $option) {
            $option->postInstall($version, $input, $output);
        }

        foreach ($input->getOption('ini') as $ini) {
            if (false !== ($ini = parse_ini_string($ini))) {
                var_dump($ini, key($ini), current($ini));
                $this->getApplication()->getService('app.php.config')->setValue($version, key($ini), current($ini));
            }
        }

        $output->writeln(array(
            sprintf(PHP_EOL . 'PHP version <info>%s</info> was installed:', $version->getVersion()),
            sprintf('%s<comment>%s</comment>', self::INDENT, $this->prefix)
        ));

        return 0;
    }

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

    protected function startProgress(OutputInterface $output, $max = null, $format = '[%bar%]')
    {
        $progress = $this->getHelper('progress');

        $progress->setBarWidth(50);
        $progress->setEmptyBarCharacter($max ? '-' : '=');
        $progress->setProgressCharacter('>');
        $progress->setFormat($format);

        $progress->start($output, $max);
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Downloader
     */
    public function getDownloader()
    {
        return $this->getApplication()->getDownloader();
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                  $version
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \jubianchi\PhpSwitch\Console\Command\PHP\InstallCommand
     */
    protected function download(Version $version, OutputInterface $output)
    {
        $dest = $this->getDownloader()->getDestination($version);
        if (false === file_exists($dest)) {
            $mirror = $this->getConfiguration()->get('mirror');

            $output->writeln(array(
                sprintf(PHP_EOL . 'Downloading PHP <info>%s</info>', $version->getVersion()),
                sprintf('%s<comment>%s</comment>', self::INDENT, sprintf($version->getUrl(), $mirror))
            ));

            $this->getDownloader()->download($version, $mirror, $this->getDownloadProgressCallback($output));

            $output->writeln('');
        }

        $this->archive = $dest;

        return $this;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function getExtracter()
    {
        return $this->getApplication()->getExtracter();
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                  $version
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \jubianchi\PhpSwitch\Console\Command\PHP\InstallCommand
     */
    protected function extract(Version $version, OutputInterface $output)
    {
        $dest = $this->getExtracter()->getDestination($version);
        if (false === file_exists($dest)) {
            $output->writeln(array(
                sprintf(PHP_EOL . 'Extracting <info>%s</info>', $version->getVersion()),
                sprintf('%s<comment>%s</comment>', self::INDENT, $dest)
            ));

            $this->getExtracter()->extract($version, $this->archive, $this->getProcessCallback($output));

            $output->writeln('');
        }

        $this->source = $dest;

        return $this;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function getBuilder()
    {
        return $this->getApplication()->getBuilder();
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version                  $version
     * @param string                                            $options
     * @param int                                               $jobs
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     *
     * @return \jubianchi\PhpSwitch\Console\Command\PHP\InstallCommand
     */
    protected function install(Version $version, $options, $jobs, OutputInterface $output)
    {
        $dest = $this->getBuilder()->getDestination($version);
        $output->writeln(array(
            sprintf(PHP_EOL . 'Building <info>%s</info>', $version->getVersion()),
            sprintf('%s<comment>%s</comment>', self::INDENT, $dest)
        ));

        $this->getBuilder()->build($version, $this->source, $options, $jobs, $this->getProcessCallback($output));
        mkdir($dest . '/var/db', 0777, true);

        $ini = $this->source . DIRECTORY_SEPARATOR . 'php.ini-development';
        $destination = $dest . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'php.ini';

        $output->writeln(array(
            PHP_EOL . PHP_EOL . 'Moving configuration file',
            sprintf('%s<comment>From: %s</comment>', self::INDENT, $ini),
            sprintf('%s<comment>To: %s</comment>', self::INDENT, $destination)
        ));

        if (false === is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        copy($ini, $destination);

        $this->prefix = $dest;

        return $this;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return callable
     */
    protected function getProcessCallback(OutputInterface $output)
    {
        $self = $this;

        if (OutputInterface::VERBOSITY_VERBOSE !== $output->getVerbosity()) {
            $this->startProgress($output);
        }

        return function($type, $buffer) use ($self, $output) {
            $buffer = rtrim($buffer);
            if ('' === empty($buffer)) {
                return;
            }

            $self->log($buffer, 'err' === $type ? \Monolog\Logger::ERROR : \Monolog\Logger::INFO);
            $self->getHelper('progress')->advance();
        };
    }

    protected function getDownloadProgressCallback(OutputInterface $output)
    {
        $self = $this;

        $this->startProgress($output, 100, '[%bar%] %percent%%');

        return function($download_size, $downloaded_size, $upload_size, $uploaded_size) use ($self) {
            static $previous = 0;

            if ($download_size > 0) {
                $complete = ceil(($downloaded_size / $download_size) * 100);

                $self->getHelper('progress')->advance($complete - $previous);

                $previous = $complete;
            }
        };
    }
}
