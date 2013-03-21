<?php
namespace jubianchi\PhpSwitch\Console\Subscriber;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Console\Helper\ProgressHelper;
use jubianchi\PhpSwitch\Event;

class Builder extends Event\Subscriber
{
    public function __construct(OutputInterface $output, ProgressHelper $progress)
    {
        $afterCallback = function() use ($output) { $output->write(PHP_EOL); };
        $processCallback = function() use ($progress, $output) {
            $progress->advance();
        };
        $self = $this;

        $this
            ->handle('build.before', function(GenericEvent $event) use ($self, $output, $progress) {
                $output->writeln(array(
                    sprintf(PHP_EOL . 'Building <info>%s</info>', $event->getArgument('version')->getVersion()),
                    sprintf('    <comment>%s</comment>', $event->getArgument('source')),
                    sprintf('    <comment>%s</comment>', $event->getArgument('prefix'))
                ));

                $self->startProgress($progress, $output);
            })
            ->handle('build.progress', $processCallback)
            ->handle('build.after', $afterCallback)
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
