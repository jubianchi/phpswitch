<?php
namespace jubianchi\PhpSwitch\PHP\Option\Disable;

class CGIOption extends DisableOption
{
    const ARG = 'disable-cgi';
    const DESC = 'Disable building CGI version of PHP. Available with PHP 4.3.0. As of PHP 5.3.0 this argument enables FastCGI which previously had to be enabled using --enable-fastcgi.';
}
