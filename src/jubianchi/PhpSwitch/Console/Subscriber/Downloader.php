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

class Downloader extends Event\Subscriber
{
    public function __construct(OutputInterface $output, ProgressHelper $progress)
    {
        $afterCallback = function() use ($output) { $output->write(PHP_EOL); };
        $self = $this;

        $this
            ->handle('download.before', function(GenericEvent $event) use ($self, $output, $progress) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Downloading PHP <info>%s</info>', $event->getArgument('version')->getVersion()),
                    sprintf('    <comment>%s</comment>', sprintf($event->getArgument('version')->getUrl(), $event->getArgument('mirror')))
                ));

                if (OutputInterface::VERBOSITY_QUIET !== $output->getVerbosity()) {
                    $self->startProgress($progress, $output, 100, '[%bar%] %percent%%');
                }
            })
            ->handle('download.progress', function(GenericEvent $event) use ($progress) {
                static $previous = 0;
                static $size = 0;

                if ($size > 0) {
                    $complete = ceil(($event->getArgument('downloaded') / $size) * 100);

                    $progress->advance($complete - $previous);

                    $previous = $complete;
                } else {
                    $size = $event->getArgument('size');
                }
            })
            ->handle('download.after', $afterCallback)
        ;
    }

    public function startProgress(ProgressHelper $progress, OutputInterface $output, $max = null, $format = '[%bar%]')
    {
        $progress->setBarWidth(50);
        $progress->setEmptyBarCharacter($max ? '-' : '=');
        $progress->setProgressCharacter('>');
        $progress->setFormat($format);

        $progress->start($output, $max);
    }
}
