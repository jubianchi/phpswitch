<?php
namespace jubianchi\PhpSwitch\PHP\Option\With;

use Symfony\Component\Console\Input\InputOption;
use jubianchi\PhpSwitch\PHP\Option\Enable;

class PDOMySQLOption extends WithOption
{
    const ARG = 'pdo-mysql';
    const MODE = InputOption::VALUE_OPTIONAL;
    const DESC = 'Used to install the PDO MySQL extension, where the optional parameter is the MySQL base install directory. If mysqlnd is passed, then the MySQL native driver will be used.';

    /**
     * @return array|\jubianchi\PhpSwitch\PHP\Option\Option[]
     */
    public function requires()
    {
        return array(
            new Enable\PDOOption()
        );
    }
}
