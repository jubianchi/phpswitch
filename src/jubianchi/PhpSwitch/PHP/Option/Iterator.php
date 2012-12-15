<?php
namespace jubianchi\PhpSwitch\PHP\Option;

class Iterator extends \FilterIterator
{
    private $directory;

    public function __construct(\Iterator $iterator, $directory)
    {
        parent::__construct($iterator);

        $this->directory = realpath($directory);
    }

    public function accept()
    {
        $file = parent::current();
        require_once $file->getRealPath();

        $className = $this->getClassName($file);

        if(class_exists($className)) {
            $reflector = new \ReflectionClass($className);

            return (
                true === $reflector->isInstantiable() &&
                true === $reflector->isSubclassOf('\\jubianchi\\PhpSwitch\\PHP\\Option\\Option')
            );
        }

        return false;
    }

    public function current()
    {
        return $this->getClassName(parent::current());
    }

    protected function getClassName($file)
    {
        $className = pathinfo($file, PATHINFO_FILENAME);
        $path = preg_replace(
            '/' . preg_quote($this->directory, '/') . '/',
            '',
            dirname($file)
        );
        $namespace = str_replace('/', '\\', $path);

        return $namespace . '\\' . $className;
    }
}
