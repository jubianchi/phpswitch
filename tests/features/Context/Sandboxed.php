<?php

namespace jubianchi\PhpSwitch\Test\Context;

interface Sandboxed
{
    public function setDirectories($root, $workspace, $home, $sandbox);
} 