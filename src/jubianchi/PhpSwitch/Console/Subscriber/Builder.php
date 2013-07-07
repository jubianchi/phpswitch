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

class Builder extends Event\Subscriber
{
    public function __construct(OutputInterface $output, ProgressHelper $progress = null)
    {
        $self = $this;

        $this
            ->handle('build.before', function(GenericEvent $event) use ($self, $output, $progress) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Building <info>%s</info>', $event->getArgument('version')),
                    sprintf('    <comment>%s</comment>', $event->getArgument('source')),
                    sprintf('    <comment>%s</comment>', $event->getArgument('prefix'))
                ));

                if(null !== $progress) {
                    $self->startProgress($progress, $output);
                }
            })
            ->handle('build.after', function() use ($output) { $output->write(PHP_EOL); })
        ;

        if(null !== $progress) {
            $this
                ->handle('build.progress', function() use ($progress, $output) { $progress->advance(); })
            ;
        }
    }

    public function startProgress(ProgressHelper $progress, OutputInterface $output)
    {
        $progress->setBarWidth(50);
        $progress->setEmptyBarCharacter('-');
        $progress->setProgressCharacter('>');
        $progress->setFormat('[%bar%]');

        $progress->start($output);
    }
}
