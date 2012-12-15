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

    function __construct($directory)
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

        $home = $this->directory . DIRECTORY_SEPARATOR . '.phpswitch';

        $rootNode
            ->children()
                ->scalarNode('version')
                    ->defaultValue('')
                ->end()
                ->scalarNode('home')
                    ->defaultValue($home)
                ->end()
                ->scalarNode('downloads')
                    ->defaultValue($home . '/downloads')
                ->end()
                ->scalarNode('sources')
                    ->defaultValue($home . '/sources')
                ->end()
                ->scalarNode('install')
                    ->defaultValue($home . '/installed')
                ->end()
                ->scalarNode('mirror')
                    ->defaultValue('fr2.php.net')
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
