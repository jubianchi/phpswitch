<?php
namespace tests\units\jubianchi\PhpSwitch\Phar;

use mageekguy\atoum;
use mageekguy\atoum\mock;
use mageekguy\atoum\mock\streams;
use jubianchi\PhpSwitch\Phar\Bootstrap as TestedClass;

require_once __DIR__ . '/../../../bootstrap.php';

class Bootstrap extends atoum\test
{
    public function test__construct()
    {
        $this
            ->if($class = uniqid())
            ->then
                ->exception(function() use ($class) {
                    new TestedClass($class);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Class %s does not exist', $class))
            ->if($this->mockGenerator->generate('\\jubianchi\\PhpSwitch\\Phar\\Runnable', '\\mock'))
            ->then
                ->object($builder = new TestedClass('\\mock\\jubianchi\\PhpSwitch\\Phar\\Runnable'))->isInstanceOf('\\jubianchi\\PhpSwitch\\Phar\\Bootstrap')
        ;
    }

    public function test__toString()
    {
        $this
            ->if($this->mockGenerator->generate('\\jubianchi\\PhpSwitch\\Phar\\Runnable', '\\mock'))
            ->and($builder = new TestedClass('\\mock\\jubianchi\\PhpSwitch\\Phar\\Runnable'))
            ->then
                ->castToString($builder)->isEqualTo(<<<EOF
<?php

\$basedir = __DIR__ . DIRECTORY_SEPARATOR . '..';

require_once implode(
    DIRECTORY_SEPARATOR,
    array(
        \$basedir,
        'vendor',
        'autoload.php'
    )
);

\$app = new \mock\jubianchi\PhpSwitch\Phar\Runnable(\$basedir, array (
));
\$app->run();
EOF
                )
            ->if($args = array($key = uniqid() => $value = uniqid()))
            ->and($builder = new TestedClass('\\mock\\jubianchi\\PhpSwitch\\Phar\\Runnable', $args))
            ->then
                ->castToString($builder)->isEqualTo(<<<EOF
<?php

\$basedir = __DIR__ . DIRECTORY_SEPARATOR . '..';

require_once implode(
    DIRECTORY_SEPARATOR,
    array(
        \$basedir,
        'vendor',
        'autoload.php'
    )
);

\$app = new \mock\jubianchi\PhpSwitch\Phar\Runnable(\$basedir, array (
  '$key' => '$value',
));
\$app->run();
EOF
                )
        ;
    }
}
