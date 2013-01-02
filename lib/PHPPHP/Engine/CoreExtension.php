<?php

namespace PHPPHP\Engine;

use PHPPHP\Engine\Objects\ClassEntry;

final class CoreExtension extends Extension\Base {

    protected $name = 'Core';
    protected $namespace = __NAMESPACE__;

    public function register(\PHPPHP\Engine\Executor $executor) {
        $this->registerCoreFunctions($executor->getFunctionStore());
        $this->registerCoreConstants($executor->getConstantStore());
        parent::register($executor);
    }

    protected function registerCoreConstants(ConstantStore $constants) {
        $coreConstants = array(
            'null'  => null,
            'true'  => true,
            'false' => false,
        );
        foreach ($coreConstants as $name => $value) {
            $constants->register($name, Zval::factory($value), false);
        }
    }
    
    protected function registerCoreFunctions(FunctionStore $functions) {
        $coreFunctions = array(
            'array_merge',
            'bin2hex',
            'implode',
            'join',
            'php_uname',
            'phpversion',
            'realpath',
            'strlen',
            'var_dump',
            'zend_version',
        );

        foreach ($coreFunctions as $funcName) {
            $functions->register($funcName, new FunctionData\InternalProxy($funcName));
        }
    }

    protected function loadFunctions() {
        require_once __DIR__ . '/Functions.php';
    }

    protected function getConstants() {
        return array(
            'PHP_INT_SIZE' => PHP_INT_SIZE,
            'PHP_SAPI'     => 'cli',
            'PHP_OS'       => PHP_OS,
            'PHP_VERSION'  => PHP_VERSION,
            'E_ERROR'      => E_ERROR,
        );
    }

    protected function getClasses() {
        return array(
            new ClassEntry('stdClass'),
        );
    }
}
