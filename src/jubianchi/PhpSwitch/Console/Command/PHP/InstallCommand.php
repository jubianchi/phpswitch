<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Monolog\Logger;
use jubianchi\PhpSwitch\PHP\Option\Resolver;
use jubianchi\PhpSwitch\PHP\Finder as PHPFinder;
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

    /** @var array */
    private $options = array();

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this->addArgument('version', InputArgument::REQUIRED, 'PHP version (x.y.z)');
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new PHPFinder();
        $version = $finder->getVersion($input->getArgument('version'));

        $resolver = new Resolver();
        $options = $resolver->resolve($input, $this->options);

        $this->log(
            array(
                sprintf('Installing PHP <info>%s</info>', $version),
                sprintf('Configure options: <info>[%s]</info>', implode(', ', $options))
            ),
            Logger::INFO,
            $output
        );

        $this
            ->download($version, $output)
            ->extract($version, $output)
            ->install($version, implode(' ', $options), $output)
        ;

        $this->log(array(
            sprintf('PHP version <info>%s</info> was installed:', $version->getVersion()),
            sprintf('%s<comment>%s</comment>', self::INDENT, $this->prefix)
        ));

        return 0;
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
            var_dump(sprintf($version->getUrl(), $mirror));

            $this->log(array(
                sprintf('Downloading PHP <info>%s</info>', $version),
                sprintf('%s<comment>%</comment>', self::INDENT, sprintf($version->getUrl(), $mirror))
            ));

            $this->getDownloader()->download($version, $mirror);
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
            $this->log(array(
                sprintf('Extracting <info>%s</info>', $version),
                sprintf('%s<comment>%s</comment>', self::INDENT, $dest)
            ));

            $this->getExtracter()->extract($version, $this->archive, $this->getProcessCallback($output));
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
     * @param array                                             $options
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     *
     * @return \jubianchi\PhpSwitch\Console\Command\PHP\InstallCommand
     */
    protected function install(Version $version, $options, OutputInterface $output)
    {
        $dest = $this->getBuilder()->getDestination($version);
        if (true === file_exists($dest)) {
            throw new \RuntimeException(sprintf('PHP version %s is already installed', $version->getVersion()));
        }

        $this->log(array(
            sprintf('Building <info>%s</info>', $version),
            sprintf('%s<comment>%s</comment>', self::INDENT, $dest)
        ));

        $this->getBuilder()->build($version, $this->source, $options, $this->getProcessCallback($output));

        $ini = $this->source . DIRECTORY_SEPARATOR . 'php.ini-development';
        $destination = $dest . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'php.ini';

        $this->log(array(
            'Moving configuration file',
            sprintf('%s<comment>From: %s</comment>', self::INDENT, $ini),
            sprintf('%s<comment>To: %s</comment>', self::INDENT, $destination)
        ));
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

        return function($type, $buffer) use ($self, $output) {
            $buffer = rtrim($buffer);
            if ('' === empty($buffer)) {
                return;
            }

            if ('err' === $type) {
                $buffer = sprintf('<error>%s</error>', $buffer);
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity() || 'err' === $type) {
                $self->log($buffer, 'err' === $type ? \Monolog\Logger::ERROR : \Monolog\Logger::DEBUG);
            }
        };
    }
}
