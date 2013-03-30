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
