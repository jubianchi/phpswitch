<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\PHP\Version;
use jubianchi\PhpSwitch\Console\Command\Command;
use jubianchi\PhpSwitch\Console\Subscriber;

class InstallCommand extends Command
{
    const NAME = 'php:install';
    const DESC = 'Installs a PHP version';

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addArgument('version', InputArgument::REQUIRED, 'PHP version (x.y.z)')
            ->addOption('alias', 'a', InputOption::VALUE_REQUIRED, 'Version name alias')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Use the same configuration as an existing version')
            ->addOption('jobs', 'j', InputOption::VALUE_REQUIRED, 'Number of jobs to run simultaneously')
            ->addOption('ini', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'INI configuration directives')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Application $application
     */
    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);

        if (null !== $application) {
			$this->getApplication()->getService('app.php.options')->setCommand($this);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \InvalidArgumentException
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $finder = $this->getApplication()->getService('app.php.finder');
        $version = $finder->getVersion($input->getArgument('version'));
        $mirror = $this->getConfiguration()->get('mirror');
        if (null !== ($alias = $input->getOption('alias'))) {
            $version->setName($alias);
        }

        $template = $this->getApplication()->getService('app.php.template.builder')->build($version, $input);

        $subscriber = new Subscriber\Installer($output, $this->getHelper('progress'));
        $this->getApplication()->getService('app.event.dispatcher')->addEventSubscriber($subscriber);
        $this->getInstaller()->install($template, $mirror, $input->getOption('jobs'), $input, $output);
        $this->getApplication()->getService('app.event.dispatcher')->removeEventSubscriber($subscriber);

        $configs = $template->getConfigs();
        foreach ($configs as $key => $value) {
            $this->getApplication()->getService('app.php.config')->setValue($version, $key, $value);
        }

        $this->getConfiguration()
            ->set('versions.' . str_replace('.', '-', $version) . '.options', (string) $template->getOptions())
            ->set('versions.' . str_replace('.', '-', $version) . '.config', $configs)
            ->dump()
        ;

        return 0;
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Installer
     */
    public function getInstaller()
    {
        return $this->getApplication()->getService('app.php.installer');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Resolver
     */
    public function getResolver()
    {
        return $this->getApplication()->getOptionResolver();
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Normalizer
     */
    public function getNormalizer()
    {
        return $this->getApplication()->getOptionNormalizer();
    }

    protected function resolveOptions(InputInterface $input)
    {
        $options = $this->getResolver()->resolve($input, $this->options);

        if (null !== ($config = $input->getOption('config'))) {
            try {
                $config = $this->getConfiguration()->get('versions.' . str_replace('.', '-', $config));
            } catch (\InvalidArgumentException $exception) {
                throw new \InvalidArgumentException(
                    sprintf('Configuration %s does not exist', $config),
                    $exception->getCode(),
                    $exception
                );
            }

            $options->addOptions($this->getNormalizer()->denormalize($config, $this->options));
        }

        return $options;
    }
}
