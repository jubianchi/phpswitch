<?php
namespace tests\units\jubianchi\PhpSwitch\Phar\Filter;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Phar\Filter\CommentFilter as TestedClass;

require_once __DIR__ . '/../../../../bootstrap.php';

class CommentFilter extends atoum\test
{
    public function test__invoke()
    {
        $this
            ->if($filter = new TestedClass())
            ->and($contents = uniqid())
            ->and($tokens = array(
                array(T_OPEN_TAG, '<?php'),
                array(T_DOC_COMMENT),
                array(T_DOC_COMMENT),
                $textToken = uniqid(),
                array(T_COMMENT),
                array(T_COMMENT),
                $otherTextToken = uniqid(),
            ))
            ->then
                ->string($filter($contents, $tokens))->isEqualTo("<?php\n\n" . $textToken . "\n\n" . $otherTextToken)
        ;
    }
}
