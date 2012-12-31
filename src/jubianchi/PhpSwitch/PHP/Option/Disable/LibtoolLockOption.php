<?php
namespace jubianchi\PhpSwitch\PHP\Option\Disable;

class LibtoolLockOption extends DisableOption
{
    const ARG = 'disable-libtool-lock';
    const DESC = 'Avoid locking (might break parallel builds).';
}
