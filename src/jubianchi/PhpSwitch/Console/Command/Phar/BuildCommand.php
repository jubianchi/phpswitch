<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Command\Phar;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\Phar;

class BuildCommand extends Command
{
    const NAME = 'phar:build';
    const DESC = 'Builds phpswitch Phar';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Phar filename', 'phpswitch.phar')
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
        $name = $input->getArgument('name');
        $progress = $this->getHelper('progress');

        $builder = new Phar\Builder();
        $phar = $builder
            ->setName($name)
            ->setBasedir($basedir = __DIR__ . '/../../../../../..')
            ->addFinder(
                Finder::create()
                    ->files()
                    ->ignoreVCS(true)
                    ->ignoreDotFiles(true)
                    ->name('*.php')
                    ->name('*.twig')
                    ->notName('behat.yml')
                    ->notName('composer.json')
                    ->notName('composer.lock')
                    ->notName('README.md')
                    ->notName('Vagrantfile')
                    ->notPath('jubianchi/PhpSwitch/Console/Command/Phar')
                    ->in($basedir . DIRECTORY_SEPARATOR . 'src')
            )
            ->addFinder(
                Finder::create()
                    ->files()
                    ->ignoreVCS(true)
                    ->notName('composer.json')
                    ->notName('composer.lock')
                    ->notName('README')
                    ->notName('README.*')
                    ->notName('AUTHORS')
                    ->notName('AUTHORS.*')
                    ->notName('CHANGELOG')
                    ->notName('CHANGELOG.*')
                    ->notName('LICENSE')
                    ->notName('LICENSE.*')
                    ->notName('phpunit.xml.dist')
                    ->exclude('Tests')
                    ->exclude('test')
                    ->exclude('atoum')
                    ->in($basedir . DIRECTORY_SEPARATOR . '/vendor')
            )
            ->addFilter(new Phar\Filter\CommentFilter())
            ->addFilter(new Phar\Filter\WhitespaceFilter())
            ->addRaw(
                'bin/' . basename($name, '.phar'),
                (string) new Phar\Bootstrap(
                    '\\jubianchi\\PhpSwitch\\PhpSwitch',
                    array(
                        'app.workspace.path' => './.phpswitch'
                    )
                )
            )
            ->setStub((string) new Phar\Stub())
            ->buildPhar(
                function($total, $current, $previous) use ($output, $progress) {
                    if (0 === $current) {
                        $progress->setBarWidth(50);
                        $progress->start($output, $total);
                    } else {
                        $progress->advance($current - $previous);
                    }
                }
            )
        ;

        unset($phar);

        $output->write(PHP_EOL);
    }
}
