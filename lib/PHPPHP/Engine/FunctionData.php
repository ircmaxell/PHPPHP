<?php

namespace PHPPHP\Engine;

interface FunctionData {
    public function execute(Executor $executor, array $args, \PHPPHP\Engine\Zval\Ptr $return, \PHPPHP\Engine\Objects\ClassInstance $ci = null, \PHPPHP\Engine\Objects\ClassEntry $ce = null);

    public function isByRef();

    public function isArgByRef($n);

    public function getParam($n);

    public function getParams();
}