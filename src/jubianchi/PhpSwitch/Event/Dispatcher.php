<?php
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
	}
}
