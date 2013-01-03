<?php

namespace PHPPHP\Engine;

interface FunctionData {
    public function execute(Executor $executor, array $args, \PHPPHP\Engine\Zval\Ptr $return, \PHPPHP\Engine\Objects\ClassInstance $ci = null);

    public function isArgByRef($n);

    public function getParam($n);
}