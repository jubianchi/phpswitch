<?php
namespace tests\units\jubianchi\PhpSwitch\Event;

use mageekguy\atoum;

require_once __DIR__ . '/../../../bootstrap.php';

class Emitter extends atoum\test
{
    public function testSetDispatcher()
    {
        $this
            ->if($emitter = new \mock\jubianchi\PhpSwitch\Event\Emitter())
            ->then
                ->object($emitter->setDispatcher(new \jubianchi\PhpSwitch\Event\Dispatcher()))->isIdenticalTo($emitter)
        ;
    }

    public function testEmit()
    {
        $this
            ->if($emitter = new \mock\jubianchi\PhpSwitch\Event\Emitter())
            ->and($dispatcher = new \mock\jubianchi\PhpSwitch\Event\Dispatcher())
            ->and($emitter->setDispatcher($dispatcher))
            ->then
                ->object($emitter->emit($event = uniqid()))->isIdenticalTo($emitter)
                ->mock($dispatcher)
                    ->call('dispatch')->withArguments($event, new \jubianchi\PhpSwitch\Event\Event($event, $emitter, array()))
            ->if($emitter->emit($event = uniqid(), $args = array(uniqid() => uniqid())))
            ->then
                ->mock($dispatcher)
                    ->call('dispatch')->withArguments($event, new \jubianchi\PhpSwitch\Event\Event($event, $emitter, $args))
        ;
    }
}
