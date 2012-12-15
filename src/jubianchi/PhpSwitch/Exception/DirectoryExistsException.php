<?php
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
