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

use Symfony\Component\Console;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;

class CurrentCommand extends Command
{
    const NAME = 'php:current';
    const DESC = 'Displays current PHP version';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        parent::execute($input, $output);

        if ($this->getConfiguration()->get('enabled', true)) {
            try {
                $version = $this->getConfiguration()->get('version');
            } catch (\InvalidArgumentException $exception) {
                return 255;
            }

            $version = Version::fromString($version);

            if (true === $this->getApplication()->getService('app.php.installer')->isInstalled($version)) {
                $output->writeln((string) $version);

                return 0;
            }
        }

        return 1;
    }
}
