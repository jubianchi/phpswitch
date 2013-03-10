<?php
namespace jubianchi\PhpSwitch\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber
{
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
