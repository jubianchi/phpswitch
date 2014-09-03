<?php
/**
 * This file is part of phpswitch.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace jubianchi\PhpSwitch\Console\Helper;

use jubianchi\PhpSwitch\Configuration;
use jubianchi\PhpSwitch\PHP\Version;
use Symfony\Component\Console\Helper\Helper;

class ConfigurationHelper extends Helper
{
    protected $local;
    protected $global;

    public function __construct(Configuration $global, Configuration $local = null)
    {
        $this->global = $global;
        $this->local = $local;
    }

    public function getName()
    {
        return 'configuration';
    }

    public function getCurrentVersion()
    {
        $version = null;

        if ($this->isEnabledLocally()) {
            $version = $this->getCurrentLocalVersion();
        }

        if (null === $version && $this->isEnabledGlobally()) {
            $version = $this->getCurrentGlobalVersion();
        }

        return $version;
    }

    public function getCurrentLocalVersion()
    {
        if (null === $this->local) {
            return null;
        }

        try {
            try {
                $local = $this->local->get('version');
            } catch (\InvalidArgumentException $e) {
                $local = null;
            }

            return $this->global->get('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.version', $local);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function getCurrentGlobalVersion()
    {
        try {
            return $this->global->get('version');
        } catch(\InvalidArgumentException $e) {
            return null;
        }
    }

    public function isEnabledLocally()
    {
        if (null === $this->local) {
            return false;
        }

        return $this->global->get('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', true);
    }

    public function isExplicitlyDisabledLocally()
    {
        if (null === $this->local) {
            return false;
        }

        return $this->global->get('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled') === false;
    }

    public function isEnabledGlobally()
    {
        return $this->global->get('enabled', true);
    }

    public function isExplicitlyDisabledGlobally()
    {
        return $this->global->get('enabled') === false;
    }

    public function enableLocally()
    {
        if (null === $this->local) {
            throw new \BadMethodCallException('No local configuration found');
        }

        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', true);
    }

    public function enableGlobally()
    {
        $this->global->set('enabled', true);
    }

    public function disableLocally()
    {
        if (null === $this->local) {
            throw new \BadMethodCallException('No local configuration found');
        }

        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', false);
    }

    public function disableGlobally()
    {
        $this->global->set('enabled', false);
    }

    public function setVersionLocally(Version $version)
    {
        if (null === $this->local) {
            throw new \BadMethodCallException('No local configuration found');
        }

        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.version', (string) $version);
    }

    public function setVersionGlobally(Version $version)
    {
        $this->global->set('version', (string) $version);
    }

    protected function getLocalConfigDirectory()
    {
        if (null === $this->local) {
            return null;
        }

        return dirname($this->local->getPath());
    }
}
