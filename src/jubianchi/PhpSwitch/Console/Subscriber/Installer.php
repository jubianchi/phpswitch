<?php
namespace jubianchi\PhpSwitch\Console\Subscriber;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Console\Helper\ProgressHelper;
use jubianchi\PhpSwitch\Event;

class Installer extends Event\Subscriber
{
	function __construct(OutputInterface $output, ProgressHelper $progress)
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
					sprintf('    <comment>%s</comment>', $event->getArgument('destination'))
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
