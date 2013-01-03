<?php

namespace PHPPHP\Engine;

class Executor {
    const DO_RETURN = 1;
    const DO_SHUTDOWN = 2;

    public $executorGlobals;
    public $structureStack = array();

    protected $stack;
    protected $current;
    protected $globalScope = array();
    protected $parser;
    protected $shutdown = false;
    protected $compiler;
    protected $files = array();
    protected $output;

    protected $extensions;

    protected $functionStore;
    protected $constantStore;
    protected $classStore;

    public function __construct(FunctionStore $functionStore, ConstantStore $constantStore, ClassStore $classStore) {
        $this->executorGlobals = new ExecutorGlobals;
        $this->parser = new Parser;
        $this->compiler = new Compiler;
        $this->functionStore = $functionStore;
        $this->constantStore = $constantStore;
        $this->classStore = $classStore;

        $this->extensions = new \SplObjectStorage;
        $this->stack = new \SplStack;
    }

    public function getStack() {
        return $this->stack;
    }

    public function getOutput() {
        return $this->output;
    }

    public function setOutput(Output $output) {
        $this->output = $output;
    }

    public function hasFile($fileName) {
        return isset($this->files[$fileName]);
    }

    public function compileFile($fileName) {
        $this->compiler->setFileName($fileName, dirname($fileName));
        if (!isset($this->files[$fileName])) {
            $code = file_get_contents($fileName);
            $this->files[$fileName] = $this->parser->parse($code);
        }
        
        $ret = $this->compiler->compile($this->files[$fileName]);
        return $ret;
    }

    public function compile($code, $context) {
        $ast = $this->parser->parse($code);
        $this->compiler->setFileName($context, $this->executorGlobals->cwd);
        return $this->compiler->compile($ast);
    }

    public function execute(OpArray $opArray, array &$symbolTable = array(), FunctionData $function = null, array $args = array(), Zval $result = null, Objects\ClassInstance $ci = null) {
        if ($this->shutdown) return;
        $opArray->registerExecutor($this);
        $scope = new ExecuteData($this, $opArray, $function);
        $scope->arguments = $args;
        $scope->ci = $ci;

        if ($this->current) {
            $scope->parent = $this->current;
        }
        $this->current = $scope;
        if ($symbolTable || $function) {
            $scope->symbolTable =& $symbolTable;
        } else {
            $scope->symbolTable =& $this->executorGlobals->symbolTable;
        }
        $scope->returnValue = $result ?: Zval::ptrFactory();
        if ($function && $function->isByRef()) {
            $scope->returnValue->makeRef();
        }

        while (!$this->shutdown && $scope->opLine) {
            $ret = $scope->opLine->execute($scope);
            switch ($ret) {
                case self::DO_RETURN:
                    $this->current = $this->current->parent;
                    return;
                case self::DO_SHUTDOWN:
                    $this->shutdown = true;
                    return;
            }
        }
        if ($this->shutdown) {
            return;
        }
        die('Should never reach this point!');
    }

    public function callCallback($callback, ExecuteData $data, array $args = array(), Zval $return = null) {
        if ($callback instanceof Zval) {
            $callback = $callback->getValue();
        }
        if (is_string($callback)) {
            $this->functionStore->get($callback)->execute($data, $args, $return);
            return;
        } elseif (is_array($callback)) {
            $class = $callback[0];
            $method = $callback[1];
            if ($class instanceof Zval) {
                $class = $class->getValue();
            }
            if ($method instanceof Zval) {
                $method = $class->getValue();
            }
            if ($class instanceof Objects\ClassInstance) {
                $class->callMethod($data, (string) $method, $args, $return);
                return;
            }
            if (is_string($class)) {
                $class = $this->classStore->get($class);
            }
            if ($class instanceof Objects\ClassEntry) {
                $class->callMethod($data, null, (string) $method, $args, $return);
                return;
            }
        }
        throw new \RuntimeException('Invalid Callback Specified');
    }

    public function getCurrent() {
        return $this->current;
    }

    public function getFunctionStore() {
        return $this->functionStore;
    }

    public function getConstantStore() {
        return $this->constantStore;
    }

    public function getClassStore() {
        return $this->classStore;
    }

    public function registerExtension(Extension $extension) {
        if (!$this->extensions->contains($extension)) {
            $extension->register($this);
            $this->extensions->attach($extension);
        }
    }
}
