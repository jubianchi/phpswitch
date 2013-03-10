<?php
namespace jubianchi\PhpSwitch\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class Event extends GenericEvent
{
    public function __construct($name, $subject = null, array $arguments = array())
    {
        parent::__construct($subject, $arguments);

        $this->setName($name);
    }
}
