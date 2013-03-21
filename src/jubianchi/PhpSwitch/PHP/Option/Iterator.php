<?php
namespace jubianchi\PhpSwitch\PHP\Option;

class Iterator extends \FilterIterator
{
    private $directory;

    /**
     * @param \Iterator $iterator
     * @param string    $directory
     */
    public function __construct(\Iterator $iterator, $directory)
    {
        parent::__construct($iterator);

        $this->directory = $directory;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        $className = $this->current();

        try {
            $reflector = $this->getReflector($className);

            return (
                $reflector->isInstantiable() &&
                $reflector->isSubclassOf('\\jubianchi\\PhpSwitch\\PHP\\Option\\Option')
            );
        } catch (\ReflectionException $exception) {
            return false;
        }
    }

    public function getReflector($className)
    {
        return new \ReflectionClass($className);
    }

    /**
     * @return string
     */
    public function current()
    {
        return $this->getClassName(parent::current());
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getClassName($file)
    {
        $className = pathinfo($file, PATHINFO_FILENAME);
        $path = preg_replace(
            '/' . preg_quote($this->directory, '/') . '/',
            '',
            dirname($file)
        );
        $namespace = '\\' . str_replace('/', '\\', trim($path, '/'));

        return $namespace . '\\' . $className;
    }
}
