<?php
namespace jubianchi\PhpSwitch\PHP;

class Downloader
{
    const EXTENSION = '.tar.bz2';

    /** @var string $directory */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $mirror
     * @param callable                         $callback
     *
     * @return \jubianchi\PhpSwitch\PHP\Downloader
     */
    public function download(Version $version, $mirror, $callback = null)
    {
        $url  = sprintf($version->getUrl(), $mirror);
        $handle = fopen($this->getDestination($version), 'wb+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $handle);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (null !== $callback) {
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $callback);
        }

        curl_exec($ch);
        curl_close($ch);
        fclose($handle);

        return $this;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     *
     * @return string
     */
    public function getDestination(Version $version)
    {
        return $this->directory . DIRECTORY_SEPARATOR . Version::DEFAULT_NAME . '-' . $version->getVersion() . self::EXTENSION;
    }
}
