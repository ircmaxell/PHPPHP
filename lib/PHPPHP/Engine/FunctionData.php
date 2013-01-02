<?php

namespace PHPPHP\Engine;

interface FunctionData {
    public function execute(Executor $executor, array $args, \PHPPHP\Engine\Zval\Ptr $return);
}