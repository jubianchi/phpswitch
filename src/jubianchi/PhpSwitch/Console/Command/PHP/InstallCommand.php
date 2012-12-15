<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use jubianchi\PhpSwitch\PHP\Option\Resolver;
use jubianchi\PhpSwitch\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Finder as PHPFinder;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

class InstallCommand extends Command
{
    const NAME = 'php:install';
    const DESC = 'Installs a PHP version';

    const INDENT = '    ';

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

    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);

        foreach($application->getOptionFinder() as $option) {
            $option = new $option();
            $option->setCommand($this);
            $this->options[] = $option;
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
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

        $output->writeln(array(
            sprintf('Installing PHP <info>%s</info>', $version),
            sprintf('Configure options: <info>[%s]</info>', implode(', ', $options) )
        ));

        $this
            ->download($version, $output)
            ->extract($version, $output)
            ->install($version, implode(' ', $options), $output)
        ;

        $output->writeln(array(
            sprintf('PHP version <info>%s</info> was installed:', $version->getVersion()),
            sprintf('  <comment>%s</comment>', $this->prefix)
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
        if(false === file_exists($dest)) {
            $mirror = $this->getConfiguration()->get('mirror');
            $output->writeln(
                sprintf('Downloading PHP <info>%s</info>', $version),
                sprintf('  <comment>%</comment>', sprintf($version->getUrl(), $mirror))
            );

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
    protected function extract(Version $version, OutputInterface $output) {
        $dest = $this->getExtracter()->getDestination($version);
        if(false === file_exists($dest)) {
            $output->writeln(array(
                sprintf('Extracting <info>%s</info>', $version),
                sprintf('<info>%s</info>', $dest)
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
    protected function install(Version $version, $options, OutputInterface $output) {
        $dest = $this->getBuilder()->getDestination($version);
        if(true === file_exists($dest)) {
            throw new \RuntimeException(sprintf('PHP version %s is already installed', $version->getVersion()));
        }

        $output->writeln(
            sprintf('Building <info>%s</info>', $version),
            sprintf('  <comment>%s</comment>', $dest)
        );

        $this->getBuilder()->build($version, $this->source, $options, $this->getProcessCallback($output));

        $ini = $this->source . DIRECTORY_SEPARATOR . 'php.ini-development';
        $destination = $dest . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'php.ini';

        $output->writeln(array(
            'Moving configuration file',
            sprintf('  <comment>From: %s</comment>', $ini),
            sprintf('  <comment>To: %s</comment>', $destination)
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
        return function($type, $buffer) use($output) {
            if ('err' === $type) {
                $buffer = sprintf('<error>%s</error>', $buffer);
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->write($buffer);
            }
        };
    }
}
