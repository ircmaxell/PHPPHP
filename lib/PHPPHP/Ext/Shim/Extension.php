<?php

namespace PHPPHP\Ext\Shim;

use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\Zval;

class Extension extends \PHPPHP\Engine\Extension\Base {

    protected $isInternal = true;
    protected $name = 'Shim';
    protected $namespace = __NAMESPACE__;

    public function register(\PHPPHP\Engine\Executor $executor) {
        $ret = array();
        $aliases = require_once(__DIR__ . '/aliases.php');
        $functionStore = $executor->getFunctionStore();
        foreach ($aliases as $alias) {
            if (!$functionStore->exists($alias[0])) {
                $functionStore->register($alias[0], new FunctionData\InternalProxy($alias[0], $alias[1], $alias[2]));
            }
        }
        $this->registerConstants($executor);
    }

    protected function registerConstants(\PHPPHP\Engine\Executor $executor) {
        $store = $executor->getConstantStore();
        foreach (get_defined_constants(true) as $group => $set) {
            if ($group == 'user') continue;
            foreach ($set as $name => $value) {
                if (!$store->exists($name)) {
                    $store->register($name, Zval::factory($value));
                }
            }
        }
    }

}