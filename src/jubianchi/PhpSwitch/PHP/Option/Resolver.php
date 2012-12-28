<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputInterface;

class Resolver
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param array                                           $options
     *
     * @return array
     */
    public function resolve(InputInterface $input, array $options)
    {
        $opts = array();
        foreach ($options as $option) {
            if ($option->isEnabled($input) && false === in_array($option, $opts)) {
                $opts[] = $option;
            }
        }

        $opts = implode(' ', $opts);
        $opts = explode(' ', $opts);

        return array_unique($opts);
    }
}
