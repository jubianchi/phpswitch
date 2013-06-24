<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\PHP;

class ListCommand extends Command
{
    const NAME = 'php:list';
    const DESC = 'Lists PHP versions';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addOption('installed', 'i', InputOption::VALUE_NONE, 'Version name alias')
            ->addOption('available', 'l', InputOption::VALUE_NONE, 'Version name alias')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Version name alias')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $all = ($input->getOption('all') || (false === $input->getOption('installed') && false === $input->getOption('available')));
        if ($all || $input->getOption('installed')) {
            $output->writeln($this->getHelper('formatter')->formatBlock('Installed versions', 'info'));
            $this->listInstalled($output);
        }

        if ($all || $input->getOption('available')) {
            $output->writeln($this->getHelper('formatter')->formatBlock('Available versions', 'info'));
            $this->listAvailable($output);
        }

        return 0;
    }

    protected function listAvailable(OutputInterface $output)
    {
        $versions = $this->getApplication()->getService('app.php.finder')->getIterator(function($a, $b) {
			return version_compare((string) $a, (string) $b);
		});

        $maxlength = 0;
        array_walk(
            $versions,
            function($version) use (& $maxlength) {
                $maxlength = ($length = strlen((string) $version)) > $maxlength ? $length : $maxlength;
            }
        );

        foreach ($versions as  $version) {
            $output->writeln(
                sprintf(
                    '<info>%-' . ($maxlength + 2) . 's</info> <comment>%s</comment>',
                    $version,
                    sprintf($version->getUrl(), 'a')
                )
            );
        }
    }

    protected function listInstalled(OutputInterface $output)
    {
        $path = $this->getApplication()->getParameter('app.workspace.installed.path');
        $finder = new Finder();
        $finder
            ->in($path)
            ->directories()
            ->name('*-*')
            ->depth(0)
        ;

        $versions = array();
        $maxlength = 0;
        foreach ($finder as $directory) {
            $version = $directory->getRelativePathname();
            $versions[$directory->getRealPath()] = $version;
            $maxlength = ($length = strlen((string) $version)) > $maxlength ? $length : $maxlength;
        }

        $pattern = '/(5\.\d+\.\d+)$/';
        uasort(
            $versions,
            function($a, $b) use ($pattern) {
                preg_match($pattern, $a, $a);
                preg_match($pattern, $b, $b);

                return version_compare(isset($b[1]) ? $b[1] : 0, isset($a[1]) ? $a[1] : 0);
            }
        );

        foreach ($versions as $path => $version) {
            $output->writeln(
                sprintf(
                    '<info>%-' . ($maxlength + 2) . 's</info> <comment>%s</comment>',
                    $version,
                    $path
                )
            );

        }
    }
}
