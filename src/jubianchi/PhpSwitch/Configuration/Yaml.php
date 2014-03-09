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

use Symfony\Component;
use jubianchi\PhpSwitch\Configuration;

class Yaml extends Configuration
{
    /**
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function read()
    {
        return file_exists($this->getPath()) ? Component\Yaml\Yaml::parse($this->getPath()) : array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Component\Yaml\Yaml::dump(array(self::ROOT => $this->configuration), 5, 2);
    }
}