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

use Symfony\Component\EventDispatcher\GenericEvent;

class Event extends GenericEvent
{
    public function __construct($name, $subject = null, array $arguments = array())
    {
        parent::__construct($subject, $arguments);

        $this->setName($name);
    }
}
