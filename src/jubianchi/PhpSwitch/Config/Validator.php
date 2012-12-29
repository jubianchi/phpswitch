<?php
namespace jubianchi\PhpSwitch\Config;

use jubianchi\PhpSwitch;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class Validator implements ConfigurationInterface
{
    const ROOT = 'phpswitch';

    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT);

        $rootNode
            ->children()
                ->scalarNode('version')
                    ->defaultValue('')
                ->end()
                ->scalarNode('mirror')
                    ->defaultValue(getenv('PHPSWITCH_MIRROR') ?: 'fr2.php.net')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    public function validate(array $values)
    {
        $processor = new Processor();

        return $processor->processConfiguration($this, $values);
    }
}
