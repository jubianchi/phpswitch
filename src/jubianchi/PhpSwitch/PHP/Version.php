<?php
namespace jubianchi\PhpSwitch\PHP;

class Version
{
    /** @var string */
    private $name;

    /** @var string */
    private $version;

    /** @var string */
    private $url;

    /**
     * @param string $name
     * @param string $url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $url = null)
    {
        $this->setName($name);

        preg_match('/^php\-\s*(5\.\d+\.\d+)/', $name, $matches);
        $this->setVersion($matches[1]);

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
        if (false == preg_match('/^(php\-\s*(5\.\d+\.\d+))/', $name, $matches)) {
            throw new \InvalidArgumentException(sprintf('Wrong PHP version %s', $name));
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        return $this->name;
    }
}
