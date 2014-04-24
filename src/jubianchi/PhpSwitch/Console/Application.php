<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console;

use jubianchi\PhpSwitch\Console\Helper\ConfigurationHelper;
use Symfony\Component\Console\Application as BaseApplication;
use jubianchi\PhpSwitch\Configuration;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    /** @var \Pimple */
    private $container;

    /**
     * @param \Pimple $container
     */
    public function __construct(\Pimple $container)
    {
        $this->container = $container;

        parent::__construct('phpswitch', '0.1');

        //$this->setAutoExit(false);
    }

    /**
     * @return \jubianchi\PhpSwitch\Configuration
     */
    public function getConfiguration()
    {
        return $this->getService('app.config');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Downloader
     */
    public function getDownloader()
    {
        return $this->getService('app.php.downloader');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Extracter
     */
    public function getExtracter()
    {
        return $this->getService('app.php.extracter');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Builder
     */
    public function getBuilder()
    {
        return $this->getService('app.php.builder');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Finder
     */
    public function getOptionFinder()
    {
        return $this->getService('app.php.option.finder');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Resolver
     */
    public function getOptionResolver()
    {
        return $this->getService('app.php.option.resolver');
    }

    /**
     * @return \jubianchi\PhpSwitch\PHP\Option\Normalizer
     */
    public function getOptionNormalizer()
    {
        return $this->getService('app.php.option.normalizer');
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->getService('app.logger');
    }

    /**
     * @return \Pimple
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $service
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function getService($service)
    {
        if (null === $this->container) {
            throw new \RuntimeException(sprintf('No service container defined'));
        }

        return $this->container[$service];
    }

    public function getParameter($name)
    {
        $parameters = $this->getService('parameters');

        return $parameters[$name];
    }

    protected function getDefaultHelperSet()
    {
        $helperSet =  parent::getDefaultHelperSet();

        if(isset($this->container['app.config.local']) && isset($this->container['app.config.user'])) {
            $helperSet->set(new ConfigurationHelper($this->getService('app.config.local'), $this->getService('app.config.user')));
        }

        return $helperSet;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (0 === posix_getuid() && 0 !== getmyuid()) {
            throw new \RuntimeException(
                sprintf(
                    'You should not run %s as root. It will automatically ask for privileges when required.',
                    $this->getName()
                )
            );
        }

        return parent::doRun($input, $output);
    }
}
