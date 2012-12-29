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

        if (class_exists($className)) {
            $reflector = new \ReflectionClass($className);

            return (
                true === $reflector->isInstantiable() &&
                true === $reflector->isSubclassOf('\\jubianchi\\PhpSwitch\\PHP\\Option\\Option')
            );
        }

        return false;
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
