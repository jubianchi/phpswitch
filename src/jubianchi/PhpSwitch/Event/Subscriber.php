<?php
namespace jubianchi\PhpSwitch\Event;

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
