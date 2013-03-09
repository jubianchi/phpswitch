<?php
namespace jubianchi\PhpSwitch\Phar;

class Stub
{
    public function __toString()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php

$PHAR = basename(__FILE__);
$ROOT = 'phar://' . $PHAR . DIRECTORY_SEPARATOR;
$BIN = $ROOT . 'bin' . DIRECTORY_SEPARATOR . basename($PHAR, '.phar');

Phar::mapPhar($PHAR);

if (file_exists($BIN)) {
    require $BIN;
}

__HALT_COMPILER();
EOF;
    }
}
