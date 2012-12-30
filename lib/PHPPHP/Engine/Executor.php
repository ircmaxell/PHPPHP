<?php

namespace PHPPHP\Engine;

class Executor {
    const DO_RETURN = 1;

    public $executorGlobals;
    protected $stack = array();
    protected $current;
    protected $globalScope = array();
    protected $functions = array();
    protected $constantsStore;
    protected $parser;
    protected $compiler;
    protected $files = array();

    public function __construct(ConstantsStore $constantsStore) {
        $this->executorGlobals = new ExecutorGlobals;
        $this->parser = new Parser;
        $this->compiler = new Compiler;
        $this->constantsStore = $constantsStore;
    }

    public function hasFile($fileName) {
        return isset($this->files[$fileName]);
    }

    public function compileFile($fileName) {
        if (!isset($this->files[$fileName])) {
            $code = file_get_contents($fileName);
            $this->files[$fileName] = $this->parser->parse($code);
        }
        return $this->compiler->compile($this->files[$fileName]);
    }

    public function compile($code) {
        $ast = $this->parser->parse($code);
        return $this->compiler->compile($ast);
    }

    public function execute(array $opLines, array $symbolTable = array()) {
        $scope = new ExecuteData($this, $opLines);
        if ($this->current) {
            $scope->parent = $this->current;
        }
        $this->stack[] = $scope;
        $this->current = $scope;
        if ($symbolTable) {
            $scope->symbolTable = $symbolTable;
        } else {
            $scope->symbolTable =& $this->executorGlobals->symbolTable;
        }

        while ($scope->opLine) {
            $ret = $scope->opLine->handler->execute($scope);
            switch ($ret) {
                case self::DO_RETURN:
                    array_pop($this->stack);
                    if ($scope->parent) {
                        $scope->parent->opLine->result->zval = $scope->returnValue->zval;
                    }
                    $this->current = end($this->stack);
                    return;
            }
        }
    }

    public function addFunction($name, FunctionData $func) {
        $name = strtolower($name);
        if (isset($this->functions[$name])) {
            throw new \RuntimeException('Duplication Function');
        }
        $this->functions[$name] = $func;
    }

    public function getFunction($name) {
        $name = strtolower($name);
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        }
        throw new \RuntimeException(sprintf('Call to undefined function %s', $name));
    }

    public function getConstantsStore() {
        return $this->constantsStore;
    }
}
