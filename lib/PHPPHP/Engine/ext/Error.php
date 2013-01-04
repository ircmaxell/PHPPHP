<?php

namespace PHPPHP\Engine;

return array(
    'error_reporting' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $return->setValue(0);
        },
        false,
        array(
            new ParamData('level', false, '', true),
        )
    ),
);