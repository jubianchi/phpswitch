<?php
namespace jubianchi\PhpSwitch\PHP;

class Version
{
    const DEFAULT_NAME = 'php';

    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /** @var string */
    private $url;

    public static function fromString($version)
    {
        $infos = explode('-', $version);

        $name = implode('-', array_slice($infos, 0, -1));
        $version = current(array_slice($infos, -1));

        return new static($version, null, $name);
    }

    /**
     * @param string $version
     * @param string $name
     * @param string $url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($version, $url = null, $name = null)
    {
        $this->setVersion($version);
        $this->setName($name);

        if (null !== $url) {
            $this->setUrl($url);
        }
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \jubianchi\PhpSwitch\PHP\Version
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name ?: self::DEFAULT_NAME;
    }

    /**
     * @param string $url
     *
     * @return \jubianchi\PhpSwitch\PHP\Version
     */
    public function setUrl($url)
    {
        if (false == preg_match('/^http:\/\//', $url)) {
            $url = 'http://php.net/' . ltrim($url, '/');
        }

        $this->url = preg_replace('/\/from\/a\/mirror/', '/from/%s/mirror', $url);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $version
     *
     * @return \jubianchi\PhpSwitch\PHP\Version
     */
    public function setVersion($version)
    {
        if (false == preg_match('/^(\d\.(?:\d+\.?)+)/', $version)) {
            throw new \InvalidArgumentException(sprintf('Wrong PHP version %s', $version));
        }

        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . '-' . $this->getVersion();
    }
}
