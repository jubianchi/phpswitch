<?php
namespace jubianchi\PhpSwitch\Event;

abstract class Emitter
{
    /** @var \jubianchi\PhpSwitch\Event\Dispatcher */
    protected $dispatcher;

    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    public function emit($name, array $args = array())
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch($name, new Event($name, $this, $args));
        }

        return $this;
    }
}
