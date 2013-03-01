<?php
namespace jubianchi\PhpSwitch\Phar\Filter;

class CommentFilter implements Filter
{
    public function __invoke($contents, array $tokens)
    {
        $contents = '';
        foreach ($tokens as $token) {
            if (is_array($token) && in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $contents .= "\n";
            } else {
                $contents .= is_array($token) ? $token[1] : $token;
            }
        }

        return $contents;
    }
}
