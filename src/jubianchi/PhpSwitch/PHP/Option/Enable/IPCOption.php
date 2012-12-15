<?php
namespace jubianchi\PhpSwitch\PHP\Option\Enable;

use jubianchi\PhpSwitch\PHP\Option\Option;

class IPCOption extends Option
{
    const ARG = 'ipc';
    const ALIAS = '--enable-shmop --enable-sysvsem --enable-sysvshm --enable-sysvmsg';
}
