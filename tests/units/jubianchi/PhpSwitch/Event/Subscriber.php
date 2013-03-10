<?php
namespace tests\units\jubianchi\PhpSwitch\Event;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Event\Subscriber as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Subscriber extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($subscriber = new TestedClass())
            ->then
                ->array($subscriber->getHandlers())->isEmpty()
        ;
    }

    public function testHandle()
    {
        $this
            ->if($subscriber = new TestedClass())
            ->then
                ->object($subscriber->handle($event = uniqid(), $hanlder = function() {}))->isIdenticalTo($subscriber)
                ->array($subscriber->getHandlers())->isIdenticalTo(array($event => $hanlder))
            ->if($subscriber->handle($otherEvent = uniqid(), $otherHanlder = function() {}))
            ->then
                ->array($subscriber->getHandlers())->isIdenticalTo(array($event => $hanlder, $otherEvent => $otherHanlder))
        ;
    }
}
