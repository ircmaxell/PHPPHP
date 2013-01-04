<?php

namespace PHPPHP\Engine;

class Executor {
    const DO_RETURN = 1;
    const DO_SHUTDOWN = 2;

    public $executorGlobals;
    public $structureStack = array();

    protected $errorHandler;
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
        $this->errorHandler = new ErrorHandler\Internal;
    }
    
    public function shutdown() {
        $this->shutdown = true;
    }
    
    public function getErrorHandler() {
        return $this->errorHandler;
    }
    
    public function setErrorHandler(ErrorHandler $handler) {
        $this->errorHandler = $handler;
    }
    
    public function raiseError($level, $message) {
        $file = $this->current->opArray->getFileName();
        $line = $this->current->opLine->lineno;
        $this->errorHandler->handle($this, $level, $message, $file, $line);
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
            $this->files[$fileName] = $this->parseCode($code, $fileName);
        }
        return $this->compileCode($this->files[$fileName], $fileName);
    }

    public function compile($code, $context) {
        $ast = $this->parseCode($code, $context);
        $this->compiler->setFileName($context, $this->executorGlobals->cwd);
        return $this->compileCode($ast, $context);
    }
    
    protected function compileCode(array $ast, $file) {
        try {
            return $this->compiler->compile($ast);
        } catch (CompilerException $e) {
            $line = $e->getRawLine();
            $this->errorHandler->handle($this, E_COMPILE_ERROR, $message, $file, $line);
            $this->raiseError(E_COMPILE_ERROR, $message);
            throw new ErrorOccurredException($message, E_COMPILE_ERROR);
        }
    }
    
    protected function parseCode($code, $file) {
        try {
            return $this->parser->parse($code);
        } catch (\PHPParser_Error $e) {
            $message = 'syntax error, ' . str_replace('Unexpected', 'unexpected', $e->getMessage());
            $line = $e->getRawLine();
            $this->errorHandler->handle($this, E_PARSE, $message, $file, $line);
            throw new ErrorOccurredException($message, E_PARSE);
        }
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
            try {
                $ret = $scope->opLine->execute($scope);
            } catch (ErrorOccurredException $e) {
                $ret = false;
                // Ignored here, since the handler will shutdown for us
            }
            switch ($ret) {
                case self::DO_RETURN:
                    $this->current = $this->current->parent;
                    return;
                case self::DO_SHUTDOWN:
                    $this->shutdown();
                    return;
            }
        }
        if ($this->shutdown) {
            return;
        }
        die('Should never reach this point!');
    }

    public function getCallback($callback) {
        if ($callback instanceof Zval) {
            $callback = $callback->getValue();
        }
        if (is_string($callback)) {
            $cb = $this->functionStore->get($callback);
            return function(Executor $executor, array $args = array(), Zval $return = null) use ($cb) {
                return $cb->execute($executor, $args, $return);
            };
        } elseif (is_array($callback)) {
            $class = $callback[0];
            $method = $callback[1];
            if ($class instanceof Zval) {
                $class = $class->getValue();
            }
            if ($method instanceof Zval) {
                $method = $method->getValue();
            }
            if ($class instanceof Objects\ClassInstance) {
                return function(Executor $executor, array $args = array(), Zval $return = null) use ($class, $method) {
                    $class->callMethod($executor->getCurrent(), (string) $method, $args, $return);
                };
            }
            if (is_string($class)) {
                $class = $this->classStore->get($class);
            }
            if ($class instanceof Objects\ClassEntry) {
                // Static method!
                return function(Executor $executor, array $args = array(), Zval $return = null) use ($class, $method) {
                    $class->callMethod($executor->getCurrent(), null, (string) $method, $args, $return);
                };
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

    public function getExtensions() {
        return $this->extensions;
    }

    public function registerExtension(Extension $extension) {
        if (!$this->extensions->contains($extension)) {
            $extension->register($this);
            $this->extensions->attach($extension);
        }
    }
}
