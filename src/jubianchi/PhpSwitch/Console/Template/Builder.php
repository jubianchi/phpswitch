<?php
namespace jubianchi\PhpSwitch\Console\Template;

use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Option\Resolver;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Config\Configuration;
use jubianchi\PhpSwitch\PHP\Template;
use jubianchi\PhpSwitch\PHP\Option\Normalizer;
use jubianchi\PhpSwitch\PHP\Option\OptionCollection;

class Builder
{
	protected $resolver;
	protected $config;
	protected $normalizer;

	public function __construct(Resolver $resolver, Normalizer $normalizer, Configuration $config)
	{
		$this->resolver = $resolver;
		$this->config = $config;
		$this->normalizer = $normalizer;
	}

	public function build(Version $version, InputInterface $input)
	{
		if (null !== ($template = $this->getTemplate($input))) {
			$options = $this->normalizer->denormalize($template['options']);
			$configs = $template['config'];
		} else {
			$options = new OptionCollection();
			$configs = array();
		}

		$template = new Template($version);
		$template
			->setOptions($options->merge($this->resolver->resolve($input)))
			->setConfigs(array_merge($configs, $this->getInis($input)))
		;

		return $template;
	}

	protected function getTemplate(InputInterface $input)
	{
		if (null !== ($config = $input->getOption('config'))) {
			try {
				$config = $this->config->get('versions.' . str_replace('.', '-', $config));
			} catch (\InvalidArgumentException $exception) {
				throw new \InvalidArgumentException(
					sprintf('Configuration %s does not exist', $config),
					$exception->getCode(),
					$exception
				);
			}

			return array_merge(
				array(
					'options' => '',
					'configs' => array()
				),
				$config
			);
		}

		return null;
	}

	protected function getInis(InputInterface $input)
	{
		$configs = array();
		$ini = $input->getOption('ini');

		foreach ($ini as $directive) {
			if (false !== ($directive = parse_ini_string($directive))) {
				$key = key($directive);
				$value = current($directive);

				$configs[$key] = $value;
			}
		}

		return $configs;
	}
}
