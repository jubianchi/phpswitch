<?php
namespace jubianchi\PhpSwitch\Console\Command;

class Iterator extends \FilterIterator
{
    /** @var string */
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

        try{
            $reflector = $this->getReflector($className);

            return (
                true === $reflector->isInstantiable() &&
                true === $reflector->isSubclassOf('\\jubianchi\\PhpSwitch\\Console\\Command\\Command')
            );
        } catch(\ReflectionException $exception) {
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
