<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Subscriber;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Console\Helper\ProgressHelper;
use jubianchi\PhpSwitch\Event;

class Installer extends Event\Subscriber
{
    public function __construct(OutputInterface $output, ProgressHelper $progress)
    {
        $this
            ->handle('install.before', function(GenericEvent $event) use ($output) {
                $output->writeln(
                    array(
                        sprintf('Installing PHP <info>%s</info>', $event->getArgument('version')->getVersion()),
                        sprintf('From mirror <info>%s</info>', $event->getArgument('mirror')),
                        sprintf('Configure options: <info>[%s]</info>', $event->getArgument('options'))
                    )
                );
            })
            ->handle('install.after', function(GenericEvent $event) use ($output) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'PHP version <info>%s</info> was installed:', $event->getArgument('version')),
                    sprintf('    <comment>%s</comment>', $event->getArgument('destination')),
                    sprintf(PHP_EOL . 'Use <info>php switch %s</info> to enable it', $event->getArgument('version'))
                ));
            })
        ;

        $downloader = new Downloader($output, $progress);
        $extracter = new Extracter($output, $progress);
        $builder = new Builder($output, $progress);

        $this->handlers = array_merge(
            $this->handlers,
            $downloader->getHandlers(),
            $extracter->getHandlers(),
            $builder->getHandlers()
        );
    }
}
