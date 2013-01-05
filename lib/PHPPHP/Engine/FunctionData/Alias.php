<?php

namespace PHPPHP\Engine\FunctionData;

use PHPPHP\Engine;

class Alias extends Base {
    protected $alias;

    public function __construct(\PHPPHP\Engine\FunctionData $func) {
        $this->alias  = $func;
        $this->byRef  = $func->isByRef();
        $this->params = $func->getParams();
    }

    public function execute(Engine\Executor $executor, array $args, Engine\Zval\Ptr $return, \PHPPHP\Engine\Objects\ClassInstance $ci = null, \PHPPHP\Engine\Objects\ClassEntry $ce = null) {
        $this->alias->execute($executor, $args, $return, $ci, $ce);
    }

}
