<?php
namespace jubianchi\PhpSwitch\PHP\Option;

use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Option\Option;

class Resolver
{
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \jubianchi\PhpSwitch\PHP\Option\Option[]        $options
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\OptionCollection
     */
    public function resolve(InputInterface $input, array $options)
    {
        $opts = array();
        foreach ($options as $option) {
            if ($option->isEnabled($input) && false === in_array($option, $opts)) {
                $option->setValue($input->getOption($option->getName()));

                if (false === ($option instanceof AliasOption)) {
                    $opts[] = $option;
                }

                $opts = array_merge($opts, $this->requires($option));
            }
        }

        return new OptionCollection($opts);
    }

    /**
     * @param \jubianchi\PhpSwitch\PHP\Option\Option $option
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    protected function requires(Option $option)
    {
        $opts = array();
        $requires = $option->requires();

        if (count($requires) > 0) {
            foreach ($requires as $require) {
                $opts = array_merge($opts, $this->requires($require));
            }

            $opts = array_merge($opts, $requires);
        }

        return $opts;
    }
}
