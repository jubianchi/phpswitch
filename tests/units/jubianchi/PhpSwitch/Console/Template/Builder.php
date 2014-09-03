<?php
namespace tests\units\jubianchi\PhpSwitch\Console\Template;

use jubianchi\PhpSwitch\Console\Application;
use mageekguy\atoum;
use jubianchi\PhpSwitch\Console\Template\Builder as TestedClass;
use jubianchi\PhpSwitch\PHP\Option;
use jubianchi\PhpSwitch\Configuration;
use mageekguy\atoum\mock\controller;

require_once __DIR__ . '/../../../../bootstrap.php';

class Builder extends atoum\test
{
    public function testBuild()
    {
        $this
            ->if($collection = new Option\OptionCollection())
            ->and($resolver = new Option\Resolver($collection))
            ->and($normalizer = new Option\Normalizer($collection))
            ->and($controller = new controller())
            ->and($controller->read = array())
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Configuration(uniqid(), null, $controller))
            ->and($this->calling($configuration)->read = array(
                'versions' => array()
            ))
            ->and($config = new Configuration\Collection())
            ->and($config->add($configuration))
            ->and($builder = new TestedClass($resolver, $normalizer, $config))
            ->and($version = new \jubianchi\PhpSwitch\PHP\Version(phpversion()))
            ->and($input = new \mock\Symfony\Component\Console\Input\InputInterface())
            ->then
                ->object($template = $builder->build($version, $input))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Template')
                ->sizeOf($template->getOptions())->isEqualTo(0)
                ->array($template->getConfigs())->isEmpty()
                ->object($template->getVersion())->isIdenticalTo($version)
            ->if($option = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($option)->getAlias = '--option')
            ->and($this->calling($option)->getName = 'option')
            ->and($otherOption = new \mock\jubianchi\PhpSwitch\PHP\Option\Option())
            ->and($this->calling($otherOption)->getAlias = '--otherOption')
            ->and($this->calling($otherOption)->getName = 'otherOption')
            ->and($collection->addOptions($opts = array($option, $otherOption)))
            ->and($this->calling($input)->getOption = function($opt) {
                switch ($opt) {
                    case 'option':
                        return true;

                    case 'otherOption':
                        return false;

                    case 'ini':
                        return array();

                    case 'config':
                        return null;

                    default:
                        return uniqid();
                }
            })
            ->then
                ->object($template = $builder->build($version, $input))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Template')
                ->object($template->getOptions())->isEqualTo(new Option\OptionCollection(array($option)))
                ->array($template->getConfigs())->isEmpty()
                ->object($template->getVersion())->isIdenticalTo($version)
            ->if($templateName = uniqid())
            ->and($this->calling($input)->getOption = function($opt) use($templateName) {
                switch ($opt) {
                    case 'option':
                        return true;

                    case 'otherOption':
                        return false;

                    case 'ini':
                        return array();

                    case 'config':
                        return $templateName;

                    default:
                        return uniqid();
                }
            })
            ->then
                ->exception(function() use($builder, $version, $input) {
                    $builder->build($version, $input);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Template configuration %s does not exist', $templateName))
            ->if($controller = new controller())
            ->and($controller->read = array(
                    'versions' => array(
                        $templateName => array(
                            'options' => '--otherOption',
                            'config' => array()
                        )
                    )
                )
            )
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Configuration(uniqid(), null, $controller))
            ->and($config = new Configuration\Collection())
            ->and($config->add($configuration))
            ->and($builder = new TestedClass($resolver, $normalizer, $config))
            ->then
                ->object($template = $builder->build($version, $input))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Template')
                ->object($template->getOptions())->isEqualTo(new Option\OptionCollection(array($option, $otherOption)))
            ->if($ini = array(uniqid() => uniqid()))
            ->and($this->calling($input)->getOption = function($opt) use($ini) {
                switch ($opt) {
                    case 'ini':
                        return array(key($ini) . '=' . current($ini));

                    default:
                        return null;
                }
            })
            ->then
                ->object($template = $builder->build($version, $input))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Template')
                ->array($template->getConfigs())->isEqualTo($ini)
            ->if($controller = new controller())
            ->and($controller->read = array(
                    'versions' => array(
                        $templateName => array(
                            'options' => '--otherOption',
                            'config' => array(
                                $iniKey = uniqid() => $iniValue = uniqid()
                            )
                        )
                    )
                )
            )
            ->and($configuration = new \mock\jubianchi\PhpSwitch\Configuration(uniqid(), null, $controller))
            ->and($config = new Configuration\Collection())
            ->and($config->add($configuration))
            ->and($builder = new TestedClass($resolver, $normalizer, $config))
            ->and($this->calling($input)->getOption = function($opt) use($ini, $templateName) {
                switch ($opt) {
                    case 'ini':
                        return array(key($ini) . '=' . current($ini));

                    case 'config':
                        return $templateName;

                    default:
                        return null;
                }
            })
            ->then
                ->object($template = $builder->build($version, $input))->isInstanceOf('\\jubianchi\\PhpSwitch\\PHP\\Template')
                ->array($template->getConfigs())->isEqualTo(array_merge(array($iniKey => $iniValue), $ini))
        ;
    }
}
