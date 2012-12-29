<?php
namespace jubianchi\PhpSwitch\PHP;

use Symfony\Component\Process\Process;

class Extracter
{
    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string   $version
     * @param string   $file
     * @param callable $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function extract(Version $version, $file, $callback = null)
    {
        $basename = 'php-' . $version->getVersion();
        $dirname = dirname($file);

        $process = new Process(
            sprintf('tar -xvf %s', escapeshellarg($file)),
            $dirname
        );
        $process->run($callback);

        $process = new Process(
            sprintf(
                'mv -f %s %s',
                escapeshellarg($basename),
                $this->getDestination($version)
            ),
            $dirname
        );
        $process->run($callback);

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     *
     * @return string
     */
    public function getDestination(Version $version)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $version->getName() . '-' . $version->getVersion();
    }
}
