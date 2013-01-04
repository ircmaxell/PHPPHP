<?php

namespace PHPPHP\Engine\ErrorHandler;

class Internal implements \PHPPHP\Engine\ErrorHandler {
    
    public function handle(\PHPPHP\Engine\Executor $executor, $level, $message, $file, $line) {
        if ($executor->executorGlobals->error_reporting & $level) {
            $prefix = static::getErrorLevelName($level);
            $output = sprintf("%s: %s in %s on line %d", $prefix, $message, $file, $line);
            if ($executor->executorGlobals->display_errors) {
                $executor->getOutput()->write("\n$output\n");
            }
            if ($level & (E_PARSE | E_ERROR | E_COMPILE_ERROR)) {
                $executor->shutdown();
            }
        }
    }
    
    public static function getErrorLevelName($level) {
        switch ($level) {
            case E_PARSE:
                return 'Parse error';
            case E_COMPILE_ERROR:
            case E_ERROR:
                return 'Fatal error';
            case E_WARNING:
                return 'Warning';
            case E_NOTICE;
                return 'Notice';
            default:
                throw new \LogicException('Invalid error level specified');
        }
    }
}