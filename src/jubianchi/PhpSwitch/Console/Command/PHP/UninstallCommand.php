<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

class UninstallCommand extends Command
{
    const NAME = 'php:uninstall';
    const DESC = 'Uninstalls a PHP version';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('version', InputArgument::REQUIRED, 'PHP version (name-x.y.z)')
            ->addOption('sources', 's', InputOption::VALUE_NONE, 'Remove sources')
            ->addOption('archive', 'r', InputOption::VALUE_NONE, 'Remove downloaded archive')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Remove all')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        if ($version === $this->getConfiguration()->get('version', null)) {
            throw new \InvalidArgumentException('Cannot uninstall current php version');
        }

        $path = $this->getApplication()->getParameter('app.workspace.installed.path');
        $finder = new Finder();
        $finder
            ->in($path)
            ->directories()
            ->name($version)
            ->depth(0)
        ;

        if (0 === count($finder)) {
            throw new \InvalidArgumentException(sprintf('Version %s is not installed', $version));
        }

        $iterator = $finder->getIterator();
        $iterator->rewind();
        $version = Version::fromString($iterator->current()->getRelativePathname());

        if ($input->getOption('archive') || $input->getOption('all')) {
            $archive = $this->getApplication()->getDownloader()->getDestination($version);
            $output->writeln(array(
                sprintf(PHP_EOL . 'Removing <info>downloaded archive</info> for <info>%s</info>', $version),
                sprintf('%s<comment>%s</comment>', self::INDENT, $archive)
            ));
            unlink($archive);
        }

        if ($input->getOption('sources') || $input->getOption('all')) {
            $sources = $this->getApplication()->getExtracter()->getDestination($version);
            $output->writeln(array(
                sprintf(PHP_EOL . 'Removing <info>sources</info> for <info>%s</info>', $version),
                sprintf('%s<comment>%s</comment>', self::INDENT, $sources)
            ));
            static::deleteDirectory($sources);
        }

        $install = $this->getApplication()->getBuilder()->getDestination($version);
        $output->writeln(array(
            sprintf(PHP_EOL . 'Removing <info>installed</info> PHP version <info>%s</info>', $version),
            sprintf('%s<comment>%s</comment>', self::INDENT, $install)
        ));
        static::deleteDirectory($install);

        $output->writeln(sprintf(PHP_EOL . 'Successfuly removed PHP version <info>%s</info>', $version));

        return 0;
    }

    private static function deleteDirectory($path) {
        $iterator = new \RecursiveDirectoryIterator($path);

        foreach ($iterator as $file ) {
            if($file->isDir()) {
                static::deleteDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($path);
    }
}
