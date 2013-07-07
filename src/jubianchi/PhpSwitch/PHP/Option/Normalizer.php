<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\PHP\Option;

class Normalizer
{
    /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection */
    protected $options;

    public function __construct(OptionCollection $options)
    {
        $this->options = $options;
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Option\OptionCollection $options
     *
     * @return string
     */
    public function normalize(OptionCollection $options)
    {
        return (string) $options;
    }

    /**
     * @param string $string
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\OptionCollection
     */
    public function denormalize($string)
    {
        $denormalized = array();
        $aliases = explode(' ', trim($string));

        foreach ($aliases as $alias) {
            if (preg_match('/(?P<alias>[a-zA-Z0-9_-]+)(?:=(?P<value>.+))?/', $alias, $matches)) {
                foreach ($this->options as $option) {
                    if ($matches['alias'] === $option->getAlias()) {
                        if (isset($matches['value'])) {
                            $option->setValue($matches['value']);
                        }

                        $denormalized[] = $option;
                        break;
                    }
                }
            }
        }

        return new OptionCollection($denormalized);
    }
}
