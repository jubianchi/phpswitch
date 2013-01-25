<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputInterface;

class Resolver
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \jubianchi\PhpSwitch\PHP\Option\Option[]        $options
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function resolve(InputInterface $input, array $options)
    {
        $opts = array();
        foreach ($options as $option) {
            if ($option->isEnabled($input) && false === in_array($option, $opts)) {
                $opts[] = $option;

                $requires = $option->requires();
                if (count($requires) > 0) {
                    $opts = array_merge($opts, $requires);
                }
            }
        }

        return array_unique($opts);
    }
}
