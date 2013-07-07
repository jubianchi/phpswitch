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

class Subscriber
{
    /** @var callable[] */
    protected $handlers = array();

    public function getHandlers()
    {
        return $this->handlers;
    }

    public function handle($event, $handler)
    {
        $this->handlers[$event] = $handler;

        return $this;
    }
}
