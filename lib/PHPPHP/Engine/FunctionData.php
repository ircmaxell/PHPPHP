<?php

namespace PHPPHP\Engine;

interface FunctionData {
    public function execute(Executor $executor, array $args, ZvalPtr $return);
}