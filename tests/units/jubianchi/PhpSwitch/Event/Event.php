<?php
namespace tests\units\jubianchi\PhpSwitch\Event;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Event\Event as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Event extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($event = new TestedClass($name = uniqid()))
            ->then
                ->string($event->getName())->isIdenticalTo($name)
                ->variable($event->getSubject())->isNull()
                ->array($event->getArguments())->isEmpty()
            ->if($event = new TestedClass($name = uniqid(), $subject = new \mock\Subject()))
            ->then
                ->string($event->getName())->isIdenticalTo($name)
                ->object($event->getSubject())->isIdenticalTo($subject)
                ->array($event->getArguments())->isEmpty()
            ->if($event = new TestedClass($name = uniqid(), $subject = new \mock\Subject(), $args = array(uniqid() => uniqid())))
            ->then
                ->string($event->getName())->isIdenticalTo($name)
                ->object($event->getSubject())->isIdenticalTo($subject)
                ->array($event->getArguments())->isIdenticalTo($args)
        ;
    }
}
