<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CURLOption extends WithOption
{
    const ARG = 'curl';
    const ALIAS = '--with-curl';
    const MODE = InputOption::VALUE_OPTIONAL;
    const DEFAULT_VALUE = 'auto';

    public function getValue()
    {
        $value = parent::getValue();

        if ('auto' === $value) {
            return $this->getCurlPrefix();
        }

        return $value;
    }

    protected function getCurlPrefix()
    {
        $result = $status = null;
        exec('command -v curl-config', $result, $status);

        if (0 !== $status) {
            throw new \RuntimeException('Could not find curl-config utility');
        }

        exec($result[0] . ' --prefix', $result, $status);

        if (0 !== $status) {
            throw new \RuntimeException('Could not find curl prefix');
        }

        return $result[1];
    }
}
