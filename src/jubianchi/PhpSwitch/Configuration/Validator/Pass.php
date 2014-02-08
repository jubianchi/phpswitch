<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Configuration\Validator;

use Symfony\Component\Config\Definition\Processor;

class Pass
{
    /**
     * @param array                                          $values
     * @param \Symfony\Component\Config\Definition\Processor $processor
     *
     * @return array
     */
    public function validate(array $values, Processor $processor = null)
    {
        return $values;
    }
} 