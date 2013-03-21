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

	public function subscribe(Subscriber $subscriber)
	{
		$this->dispatcher->addEventSubscriber($subscriber);

		return $this;
	}

	public function unsubscribe(Subscriber $subscriber)
	{
		$this->dispatcher->removeEventSubscriber($subscriber);

		return $this;
	}
}
