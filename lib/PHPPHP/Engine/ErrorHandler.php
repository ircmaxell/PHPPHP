<?php

namespace PHPPHP\Engine;

interface ErrorHandler {
    
    public function handle(\PHPPHP\Engine\Executor $executor, $level, $message, $file, $line);
    
}