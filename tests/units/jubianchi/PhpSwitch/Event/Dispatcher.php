<?php
namespace tests\units\jubianchi\PhpSwitch\Event;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Event\Dispatcher as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Dispatcher extends atoum\test
{
    public function testAddEventSubscriber()
    {
        $this
            ->if($dispatcher = new TestedClass())
            ->and($eventName = uniqid())
            ->and($subscriber = new \jubianchi\PhpSwitch\Event\Subscriber())
            ->and($subscriber->handle($eventName, $handler = function() use(& $handled) { $handled = true; }))
            ->then
                ->object($dispatcher->addEventSubscriber($subscriber))->isIdenticalTo($dispatcher)
            ->if($otherSubscriber = new \jubianchi\PhpSwitch\Event\Subscriber())
            ->and($otherSubscriber->handle($eventName, $otherHandler = function() use(& $otherHandled) { $otherHandled = true; }))
            ->and($dispatcher->addEventSubscriber($otherSubscriber))
            ->and($dispatcher->dispatch($eventName))
            ->then
                ->boolean($handled)->isTrue()
                ->boolean($otherHandled)->isTrue()
        ;
    }
}
