<?php
namespace jubianchi\PhpSwitch\PHP\Option\Disable;

class URLFopenWrapperOption extends DisableOption
{
    const ARG = 'disable-url-fopen-wrapper';
    const DESC = 'Disable the URL-aware fopen wrapper that allows accessing files via HTTP or FTP. (not available since PHP 5.2.5)';
}
