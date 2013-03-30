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

class Bootstrap
{
    protected $class;
    protected $args;

    public function __construct($class, array $args = array())
    {
        if (false === class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exist', $class));
        }

        $this->class = $class;
        $this->args = $args;
    }

    public function __toString()
    {
        $args = var_export($this->args, true);

        return <<<EOF
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

\$app = new $this->class(\$basedir, $args);
\$app->run();
EOF;
    }
}
