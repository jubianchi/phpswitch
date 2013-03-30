<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Exception;

class DirectoryExistsException extends \Exception
{
    /**
     * @param string     $directory
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($directory = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Directory %s already exists', $directory), $code, $previous);
    }
}
