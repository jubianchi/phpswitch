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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\PHP\Version;

class ConfigCommand extends Command
{
    const NAME = 'php:config';
    const DESC = 'Get or set configuration';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('value', InputArgument::OPTIONAL)
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

        $name = $input->getArgument('name');
        $value = $input->getArgument('value');
        $version = Version::fromString($this->getConfiguration()->get('version'));

        if (null !== $value) {
            $this->getApplication()->getService('app.php.config')->setValue($version, $name, $value);
        }

        $output->writeln(
            sprintf(
                '%s = %s',
                $name,
                $this->getApplication()->getService('app.php.config')->getValue($version, $name)
            )
        );
    }
}
