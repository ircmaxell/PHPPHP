<?php

namespace PHPPHP\Ext\Strings;

use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\Zval;

class Extension extends \PHPPHP\Engine\Extension\Base {

    protected $isInternal = true;
    protected $name = 'Strings';
    protected $namespace = __NAMESPACE__;

    protected function getFunctions() {
        return [];
        return require __DIR__ . '/Functions.php';
    }

}
