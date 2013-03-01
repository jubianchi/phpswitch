<?php
namespace tests\units\jubianchi\PhpSwitch\Phar\Filter;

use mageekguy\atoum;
use jubianchi\PhpSwitch\Phar\Filter\WhitespaceFilter as TestedClass;

class WhitespaceFilter extends atoum\test
{
    public function test__invoke()
    {
        $this
            ->if($filter = new TestedClass())
            ->and($contents = "<?php\n\n\$foo   = 'foo';  \n    public function ()\n{\n\t\$foo = \$bar;\r\n\t\t\t?>")
            ->and($tokens = array())
            ->then
                ->string($filter($contents, $tokens))->isEqualTo("<?php\n\$foo = 'foo'; \npublic function ()\n{\n\$foo = \$bar;\n?>\n")
        ;
    }
}
