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

class Fetcher extends Event\Subscriber
{
    public function __construct(OutputInterface $output)
    {
		$string = '';
		$clear = function() use(& $string, $output) { $output->write("\r" . str_repeat(' ', strlen($string)) . "\r"); };

        $this
            ->handle('fetch.start', function(GenericEvent $event) use (& $string, $clear, $output) {
				$clear();
				$output->write($string = sprintf('   Fetching <info>%s</info>', $event->getArgument('url')));
            })
			->handle('fetch.parsing', function(GenericEvent $event) use (& $string, $clear, $output) {
				$clear();
				$output->write($string = sprintf('   Parsing <info>%s</info>', $event->getArgument('url')));
			})
			->handle('fetch.failed', function(GenericEvent $event) use (& $string, $clear, $output) {
				$clear();
				$output->writeln(sprintf('   <error>Failed fetching %s</error>', $event->getArgument('url')));
			})
			->handle('fetch.end', $clear);
        ;
    }
}
