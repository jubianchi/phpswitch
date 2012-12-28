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

        $this->directory = realpath($directory);
    }

    /**
     * @return bool
     */
    public function accept()
    {
        $file = parent::current();
        require_once $file->getRealPath();

        $className = $this->getClassName($file);

        if (class_exists($className)) {
            $reflector = new \ReflectionClass($className);

            return (
                true === $reflector->isInstantiable() &&
                true === $reflector->isSubclassOf('\\jubianchi\\PhpSwitch\\Console\\Command\\Command')
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
        $namespace = str_replace('/', '\\', $path);

        return $namespace . '\\' . $className;
    }
}
