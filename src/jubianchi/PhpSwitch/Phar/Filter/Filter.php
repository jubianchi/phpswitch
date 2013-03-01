<?php
namespace jubianchi\PhpSwitch\Phar\Filter;

interface Filter
{
    public function __invoke($contents, array $tokens);
}
