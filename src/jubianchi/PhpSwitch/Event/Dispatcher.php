<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Dispatcher extends EventDispatcher
{
    public function addEventSubscriber(Subscriber $subscriber)
    {
        foreach ($subscriber->getHandlers() as $event => $handler) {
            $this->addListener($event, $handler);
        }

        return $this;
    }

    public function removeEventSubscriber(Subscriber $subscriber)
    {
        foreach ($subscriber->getHandlers() as $event => $handler) {
            $this->removeListener($event, $handler);
        }

        return $this;
    }
}
