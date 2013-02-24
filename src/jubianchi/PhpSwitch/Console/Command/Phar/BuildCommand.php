<?php
namespace jubianchi\PhpSwitch\Console\Command\Phar;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\Phar\Phar;

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
        parent::execute($input, $output);

        clearstatcache();

        $name = $input->getArgument('name');
        $phar = new Phar(new \Phar($name));

        $phar->startBuffering();

        $sources = new Finder();
        $sources->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name('*.php')
            ->name('*.twig')
            ->notName('behat.yml')
            ->notName('composer.json')
            ->notName('composer.lock')
            ->notName('README.md')
            ->notName('Vagrantfile')
            ->notPath('jubianchi/PhpSwitch/Phar')
            ->notPath('jubianchi/PhpSwitch/Console/Command/Phar')
            ->in(__DIR__ . '/../../../../../../src')
        ;
        $vendor = new Finder();
        $vendor->files()
            ->ignoreVCS(true)
            ->notPath('atoum')
            ->notName('composer.json')
            ->notName('composer.lock')
            ->notName('README.md')
            ->exclude('Tests')
            ->in(__DIR__ . '/../../../../../../vendor')
        ;

        $files = count($sources) + count($vendor);
        $progress = $this->getHelper('progress');
        $progress->setBarWidth(50);
        $progress->start($output, $files);

        foreach ($sources as $file) {
            $phar->addFile(
                $file,
                str_replace(realpath(__DIR__ . '/../../../../../..') . '/', '', $file->getRealPath())
            );

            $this->getHelper('progress')->advance();
        }

        foreach ($vendor as $file) {
            $phar->addFile(
                $file,
                str_replace(realpath(__DIR__ . '/../../../../../..') . '/', '', $file->getRealPath())
            );

            $this->getHelper('progress')->advance();
        }

        $phar->addFromString('bin/phpswitch', $this->getBin($name), false);
        $phar->setStub($this->getStub($name));

        $phar->stopBuffering();

        unset($phar);

        $output->write(PHP_EOL);
    }

    protected function getBin($name)
    {
        return <<<EOF
<?php

use jubianchi\PhpSwitch\PhpSwitch;

require_once implode(
    DIRECTORY_SEPARATOR,
    array(
        'phar://$name',
        'vendor',
        'autoload.php'
    )
);

PhpSwitch::init('phar://$name')->run();
EOF;
    }

    protected function getStub($name)
    {
        return <<<EOF
#!/usr/bin/env php
<?php

Phar::mapPhar('$name');

require 'phar://$name/bin/phpswitch';

__HALT_COMPILER();
EOF;
    }
}
