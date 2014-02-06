<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jubianchi
 * Date: 18/09/13
 * Time: 23:37
 * To change this template use File | Settings | File Templates.
 */

namespace tests\units\jubianchi\PhpSwitch\Process;

use atoum;
use jubianchi\PhpSwitch\Process\Root as TestedClass;

class Root extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass->isSubClassOf('\\Symfony\\Component\\Process\\Process')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($process = new TestedClass($commandLine = uniqid()))
            ->then
                ->variable($process->getPassword())->isNull()
                ->string($process->getCommandLine())->isEqualTo('sudo -S ' . $commandLine . '; sudo -k')
        ;
    }

    public function testGetSetPassword()
    {
        $this
            ->if($process = new TestedClass($commandLine = uniqid()))
            ->then
                ->variable($process->getPassword())->isNull()
                ->object($process->setPassword($password = uniqid()))->isIdenticalTo($process)
                ->string($process->getPassword())->isEqualTo($password)
                ->string($process->getCommandLine())->isEqualTo('echo ' . escapeshellarg($password) . ' | sudo -S ' . $commandLine . '; sudo -k')
        ;
    }

    public function testGetSetCommandLine()
    {
        $this
            ->if($process = new TestedClass($commandLine = uniqid()))
            ->then
                ->string($process->getCommandLine())->isEqualTo('sudo -S ' . $commandLine . '; sudo -k')
                ->object($process->setCommandLine($commandLine = uniqid()))->isIdenticalTo($process)
                ->string($process->getCommandLine())->isEqualTo('sudo -S ' . $commandLine . '; sudo -k')
            ->if($process->setPassword($password = uniqid()))
            ->then
                ->string($process->getCommandLine())->isEqualTo('echo ' . escapeshellarg($password) . ' | sudo -S ' . $commandLine . '; sudo -k')
                ->object($process->setCommandLine($commandLine = uniqid()))->isIdenticalTo($process)
                ->string($process->getCommandLine())->isEqualTo('echo ' . escapeshellarg($password) . ' | sudo -S ' . $commandLine . '; sudo -k')
        ;
    }
}