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

use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Option\Option;

class Resolver
{
    /** @var \jubianchi\PhpSwitch\PHP\Option\OptionCollection */
    protected $options;

    public function __construct(OptionCollection $options)
    {
        $this->options = $options;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     *
     * @return \jubianchi\PhpSwitch\PHP\Option\OptionCollection
     */
    public function resolve(InputInterface $input)
    {
        $opts = array();
        foreach ($this->options as $option) {
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
