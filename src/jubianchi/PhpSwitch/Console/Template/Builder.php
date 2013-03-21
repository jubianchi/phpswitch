<?php
namespace jubianchi\PhpSwitch\Console\Template;

use Symfony\Component\Console\Input\InputInterface;
use jubianchi\PhpSwitch\PHP\Option\Resolver;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Config\Configuration;
use jubianchi\PhpSwitch\PHP\Template;
use jubianchi\PhpSwitch\PHP\Option\Normalizer;

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

	public function build(Version $version, InputInterface $input, array $options)
	{
		$template = new Template($version);
		$template
			->setOptions($this->resolveOptions($input, $options))
			->setConfigs(call_user_func(
				function($ini) {
					$configs = array();

					foreach ($ini as $directive) {
						if (false !== ($directive = parse_ini_string($directive))) {
							$key = key($directive);
							$value = current($directive);

							$configs[$key] = $value;
						}
					}

					return $configs;
				},
				$input->getOption('ini')
			))
		;

		return $template;
	}

	protected function resolveOptions(InputInterface $input, array $options)
	{
		$options = $this->resolver->resolve($input, $options);

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

			$options->addOptions($this->normalizer->denormalize($config, $options));
		}

		return $options;
	}
}
