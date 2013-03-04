<?php
namespace jubianchi\PhpSwitch\Phar;

interface Filter
{
    public function __invoke($contents, array $tokens);
}
