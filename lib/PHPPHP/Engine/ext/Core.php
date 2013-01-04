<?php

namespace PHPPHP\Engine;

return array(
    'register_shutdown_function' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $callback = $args[0];
            array_shift($args);
            $cb = function(Executor $executor, $oldArgs, Zval $return) use ($callback, $args) {
                $cb = $executor->getCallback($callback);
                $cb($executor, $args, $return);
            };
            $executor->registerShutdownFunction($cb);
        },
        false,
        array(
            new ParamData('callback', false, 'callable'),
            new ParamData('...', false, null, true),
        )
    ),
    'set_time_limit' => new FunctionData\Internal(
        function(Executor $executor, array $args, Zval $return) {
            $time = $args[0]->toLong();
            $executor->executorGlobals->timeLimit = $time;
            if ($time > 0) {
                $executor->executorGlobals->timeLimitEnd = time() + $time;
            } else {
                $executor->executorGlobals->timeLimitEnd = PHP_INT_MAX;
            }
        },
        false,
        array(
            new ParamData('seconds'),
        )
    ),
);