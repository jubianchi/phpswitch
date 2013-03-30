<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP;

use jubianchi\PhpSwitch\Event\Emitter;
use jubianchi\PhpSwitch\Event\Dispatcher;

class Downloader extends Emitter
{
    const EXTENSION = '.tar.bz2';

    /** @var string $directory */
    private $directory;

    /**
     * @param                                       $directory
     * @param \jubianchi\PhpSwitch\Event\Dispatcher $dispatcher
     */
    public function __construct($directory, Dispatcher $dispatcher = null)
    {
        $this->directory = $directory;

        if (null !== $dispatcher) {
            $this->setDispatcher($dispatcher);
        }
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Version $version
     * @param string                           $mirror
     *
     * @return \jubianchi\PhpSwitch\PHP\Downloader
     */
    public function download(Version $version, $mirror)
    {
        $this->emit(
            'download.before',
            $args = array(
                'version' => $version,
                'mirror' => $mirror
            )
        );

        $url  = sprintf($version->getUrl(), $mirror);
        $handle = fopen($this->getDestination($version), 'wb+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $handle);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $self = $this;
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($size, $downloaded) use ($self) {
            $self->emit(
                'download.progress',
                array(
                    'size' => $size,
                    'downloaded' => $downloaded
                )
            );
        });

        curl_exec($ch);
        curl_close($ch);
        fclose($handle);

        $this->emit('download.after', $args);

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
