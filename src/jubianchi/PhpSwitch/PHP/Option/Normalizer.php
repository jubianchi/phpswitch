<?php
namespace jubianchi\PhpSwitch\PHP\Option;

class Normalizer
{
    /**
     * @param \jubianchi\PhpSwitch\PHP\Option\Option[] $options
     *
     * @return string
     */
    public function normalize(array $options)
    {
        return implode(' ', $options);
    }

    /**
     * @param string                                   $string
     * @param \jubianchi\PhpSwitch\PHP\Option\Option[] $options
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function denormalize($string, array $options)
    {
        $denormalized = array();
        $aliases = explode(' ', trim($string));

        foreach ($aliases as $alias) {
            if (preg_match('/(?P<alias>[a-z0-9_-]+)(?:=(?P<value>.+))?/', $alias, $matches)) {
                foreach ($options as $option) {
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
