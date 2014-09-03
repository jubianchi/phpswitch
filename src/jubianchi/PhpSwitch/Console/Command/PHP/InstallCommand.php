<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Command\PHP;

use jubianchi\PhpSwitch\PHP\Exception\AlreadyInstalledException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
            ->addOption('save', 's', InputOption::VALUE_NONE, 'Save configuration locally')
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
     * @throws \jubianchi\PhpSwitch\PHP\Exception\AlreadyInstalledException
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $finder = $this->getApplication()->getService('app.php.finder.cached');
        $version = $finder->getVersion($input->getArgument('version'));
        $mirror = $this->getApplication()->getService('app.config.user')->get('mirror');
        if (null !== ($alias = $input->getOption('alias'))) {
            $version->setName($alias);
        }

        $template = $this->getApplication()->getService('app.php.template.builder')->build($version, $input);

        $subscriber = new Subscriber\Installer($output, $input->isInteractive() ? $this->getHelper('progress') : null);

        try {
            $this->getInstaller()
                ->subscribe($subscriber)
                ->install($template, $mirror, $input->getOption('jobs'), $input, $output)
                ->unsubscribe($subscriber)
            ;
        } catch(AlreadyInstalledException $exception) {
            throw new AlreadyInstalledException(
                $exception->getMessage() . PHP_EOL . 'Use --alias to install using a different name',
                $exception->getCode(),
                $exception
            );
        }

        $configs = $template->getConfigs();
        foreach ($configs as $key => $value) {
            $this->getApplication()->getService('app.php.config')->setValue($version, $key, $value);
        }

        $this->getApplication()->getService('app.config.' . ($input->getOption('save') ? 'local' : 'user'))
            ->set(
                'versions.' . $template->getName(),
                array(
                    'options' => (string) $template->getOptions(),
                    'config' => $template->getConfigs()
                ),
                $input->getOption('save')
            )
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


}
