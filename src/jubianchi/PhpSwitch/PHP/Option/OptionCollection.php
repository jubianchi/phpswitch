<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Input\InputInterface;

class OptionCollection implements OptionInterface, \Countable
{
    /** @var \jubianchi\PhpSwitch\PHP\Option\Normalizer */
    protected static $normalizer;

    /** @var \jubianchi\PhpSwitch\PHP\Option\OptionInterface[] */
    protected $options = array();

    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->addOptions($options);
        }
    }

    public static function setNormalizer(Normalizer $normalizer)
    {
        static::$normalizer = $normalizer;
    }

    public function addOptions(array $options)
    {
        $this->options = array_merge($options, $this->options);

        $this->options = array_unique($this->options);

        return $this;
    }

    public function merge(OptionCollection $collection)
    {
        return $this->addOptions($collection->options);
    }

    public function preInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        foreach ($this->options as $option) {
            $option->preInstall($version, $input, $output);
        }
    }

    public function postInstall(Version $version, InputInterface $input, OutputInterface $output)
    {
        foreach ($this->options as $option) {
            $option->postInstall($version, $input, $output);
        }
    }

    public function normalize()
    {
        return static::$normalizer->normalize($this->options);
    }

    public function count()
    {
        return count($this->options);
    }

    public function __toString()
    {
        return $this->normalize();
    }
}
