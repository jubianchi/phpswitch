<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Phar;

class Stub
{
    public function __toString()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
