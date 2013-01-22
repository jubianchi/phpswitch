<?php
namespace jubianchi\PhpSwitch\Process;

use Symfony\Component\Process\ProcessBuilder;

class Builder extends ProcessBuilder
{
    public function get(array $arguments = array())
    {
        return new static($arguments);
    }
}
