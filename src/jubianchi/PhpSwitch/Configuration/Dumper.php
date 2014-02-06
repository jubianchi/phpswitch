<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Configuration;

use Symfony\Component\Yaml\Yaml;
use jubianchi\PhpSwitch\Configuration;

class Dumper
{
    /**
     * @param string                                    $path
     * @param \jubianchi\PhpSwitch\Configuration $configuration
     *
     * @return \jubianchi\PhpSwitch\Configuration\Dumper
     */
    public function dump($path, Configuration $configuration)
    {
        file_put_contents(
            $path,
            Yaml::dump(array(Configuration::ROOT => $configuration->getValues()), 5, 2)
        );

        return $this;
    }
}
