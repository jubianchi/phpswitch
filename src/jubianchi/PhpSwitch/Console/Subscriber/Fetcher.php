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
use jubianchi\PhpSwitch\Event;

class Fetcher extends Event\Subscriber
{
    public function __construct(OutputInterface $output)
    {
        $string = '';
        $clear = function($newstring = null) use(& $string, $output) {
            $length = max(strlen($string) - strlen($newstring), 0);
            $newstring = str_pad($newstring, strlen($string), ' ');

            $output->write(
                str_repeat("\010", strlen($string)) .
                $newstring .
                str_repeat("\010", $length)
            );

            $string = $newstring;
        };

        $this
            ->handle('fetch.start', function(GenericEvent $event) use (& $string, $clear, $output) {
                $clear(sprintf('   Fetching <info>%s</info>', $event->getArgument('url')));
            })
            ->handle('fetch.parsing', function(GenericEvent $event) use (& $string, $clear, $output) {
                $clear(sprintf('   Parsing <info>%s</info>', $event->getArgument('url')));
            })
            ->handle('fetch.failed', function(GenericEvent $event) use ($clear, $output) {
                $clear();
                $output->writeln(sprintf('   <error>Failed to fetch %s</error>', $event->getArgument('url')));
            })
            ->handle('fetch.end', function() use ($clear) {
                $clear();
            });
        ;
    }
}
