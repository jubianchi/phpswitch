<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Configuration\Globl;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use jubianchi\PhpSwitch\Configuration\Validator as BaseValidator;

class Validator extends BaseValidator
{
    const ROOT = 'phpswitch';

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT);

        $rootNode
            ->children()
                ->scalarNode('version')->defaultNull()->end()
                ->booleanNode('enabled')->defaultNull()->end()
                ->scalarNode('mirror')
                    ->defaultValue(getenv('PHPSWITCH_MIRROR') ?: 'fr2.php.net')
                ->end()
                ->arrayNode('versions')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('options')->end()
                            ->arrayNode('config')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
