<?php
namespace jubianchi\PhpSwitch\Phar\Filter;

class WhitespaceFilter implements Filter
{
    public function __invoke($contents, array $tokens)
    {
        $contents = preg_replace('/[ \t]+/', ' ', $contents);
        $contents = preg_replace('/(?:\r\n|\r|\n)+/', "\n", $contents);
        $contents = preg_replace('/(?:\n ?)+/', "\n", $contents);

        return trim($contents) . "\n";
    }
}
