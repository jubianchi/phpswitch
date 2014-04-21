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

    public function __construct(Configuration $local, Configuration $global)
    {
        $this->local = $local;
        $this->global = $global;
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

        if (null === $version && $this->isEnabledGlobally())
        {
            $version = $this->getCurrentGlobalVersion();
        }

        return $version;
    }

    public function getCurrentLocalVersion()
    {
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
        return $this->global->get('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', true);
    }

    public function isExplicitlyDisabledLocally()
    {
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
        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', true);
    }

    public function enableGlobally()
    {
        $this->global->set('enabled', true);
    }

    public function disableLocally()
    {
        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.enabled', false);
    }

    public function disableGlobally()
    {
        $this->global->set('enabled', false);
    }

    public function setVersionLocally(Version $version)
    {
        $this->global->set('directories.' . str_replace('.', '\.', $this->getLocalConfigDirectory()) . '.version', (string) $version);
    }

    public function setVersionGlobally(Version $version)
    {
        $this->global->set('version', (string) $version);
    }

    protected function getLocalConfigDirectory()
    {
        return dirname($this->local->getPath());
    }
}