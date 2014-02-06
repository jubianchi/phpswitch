<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jubianchi
 * Date: 18/09/13
 * Time: 23:37
 * To change this template use File | Settings | File Templates.
 */

namespace tests\units\jubianchi\PhpSwitch\Process\Builder;

use atoum;
use jubianchi\PhpSwitch\Process\Builder;
use jubianchi\PhpSwitch\Process\Builder\Factory as TestedClass;

class Factory extends atoum
{
    public function testCreate()
    {
        $this
            ->if($factory = new TestedClass())
            ->then
                ->object($factory->create())->isEqualTo(new Builder())
                ->object($factory->create($args = array(uniqid() => uniqid())))->isEqualTo(new Builder($args))
        ;
    }
}