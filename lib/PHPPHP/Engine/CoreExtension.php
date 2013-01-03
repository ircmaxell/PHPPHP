<?php

namespace PHPPHP\Engine;

use PHPPHP\Engine\Objects\ClassEntry;

final class CoreExtension extends Extension\Base {

    protected $name = 'Core';
    protected $namespace = __NAMESPACE__;

    public function register(\PHPPHP\Engine\Executor $executor) {
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
    
    protected function registerCoreFunctions() {
        return array(
            'array_merge' => new FunctionData\InternalProxy(
                'array_merge',
                false,
                array(
                    new ParamData('array', false, 'array')
                )
            ),
            'bin2hex' => new FunctionData\InternalProxy(
                'bin2hex',
                false,
                array(
                    new ParamData('str')
                )
            ),
            'implode'      => new FunctionData\InternalProxy('implode'),
            'join'         => new FunctionData\InternalProxy('join'),
            'php_uname'    => new FunctionData\InternalProxy('php_uname'),
            'phpversion'   => new FunctionData\InternalProxy('phpversion'),
            'print_r'      => new FunctionData\InternalProxy('print_r'),
            'realpath'     => new FunctionData\InternalProxy('realpath'),
            'strlen'       => new FunctionData\InternalProxy('strlen'),
            'var_dump'     => new FunctionData\InternalProxy('var_dump'),
            'zend_version' => new FunctionData\InternalProxy('zend_version'),
        );
    }

    protected function getFunctions() {
        $funcs = require_once __DIR__ . '/ext/Functions.php';
        $funcs += require_once __DIR__ . '/ext/Array.php';
        return $funcs + $this->registerCoreFunctions();
    }

    protected function getConstants() {
        return array(
            'PHP_INT_SIZE' => PHP_INT_SIZE,
            'PHP_SAPI'     => 'cli',
            'PHP_OS'       => PHP_OS,
            'PHP_VERSION'  => PHP_VERSION,
            'E_ERROR'      => E_ERROR,

            'PHP_OUTPUT_HANDLER_START' => PHP_OUTPUT_HANDLER_START,
            'PHP_OUTPUT_HANDLER_END'   => PHP_OUTPUT_HANDLER_END,

        );
    }

    protected function getClasses() {
        return array(
            new ClassEntry('stdClass'),
        );
    }
}
