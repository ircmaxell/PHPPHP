<?php

namespace PHPPHP\Engine;

use PHPPHP\Engine\Objects\ClassEntry;
use PHPPHP\Engine\Zval\Variable;

class Compiler {

    protected $operators = array(
        'Arg' => array('ArrayOp', 'value'),

        // scalars
        'Name'           => array('ScalarOp', 'parts', '\\'),
        'Scalar_DNumber' => array('ScalarOp'),
        'Scalar_LNumber' => array('ScalarOp'),
        'Scalar_String'  => array('ScalarOp'),

        // unary operators
        'Expr_Cast_Array'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastArray', 'expr'),
        'Expr_Cast_Bool'   => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastBool', 'expr'),
        'Expr_Cast_Double' => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastDouble', 'expr'),
        'Expr_Cast_Int'    => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastInt', 'expr'),
        'Expr_Cast_String' => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastString', 'expr'),
        'Expr_Cast_Object' => array('UnaryOp', 'PHPPHP\Engine\OpLines\CastObject', 'expr'),
        'Expr_Eval'        => array('UnaryOp', 'PHPPHP\Engine\OpLines\EvalOp', 'expr'),
        'Expr_Exit'        => array('UnaryOp', 'PHPPHP\Engine\OpLines\ExitOp', 'expr'),
        'Expr_BooleanNot'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\BooleanNot'),
        'Expr_BitwiseNot'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\BitwiseNot'),
        'Expr_Empty'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\EmptyOp', 'expr'),
        'Expr_Isset'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\IssetOp', 'vars'),
        'Stmt_Unset'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\UnsetOp', 'vars'),
        'Expr_PostDec'     => array('UnaryOp', 'PHPPHP\Engine\OpLines\PostDec', 'var'),
        'Expr_PostInc'     => array('UnaryOp', 'PHPPHP\Engine\OpLines\PostInc', 'var'),
        'Expr_PreDec'      => array('UnaryOp', 'PHPPHP\Engine\OpLines\PreDec', 'var'),
        'Expr_PreInc'      => array('UnaryOp', 'PHPPHP\Engine\OpLines\PreInc', 'var'),
        'Expr_UnaryPlus'   => array('UnaryOp', 'PHPPHP\Engine\OpLines\UnaryPlus', 'expr'),
        'Expr_UnaryMinus'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\UnaryMinus', 'expr'),
        'Expr_ConstFetch'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\FetchConstant', 'name'),
        'Expr_Print'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\PrintOp', 'expr'),
        'Stmt_Return'      => array('UnaryOp', 'PHPPHP\Engine\OpLines\ReturnOp'),

        // assignment operators
        'Expr_AssignRef'        => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignRef',        'var', 'expr'),
        'Expr_AssignPlus'       => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignAdd',        'var', 'expr'),
        'Expr_AssignMinus'      => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignSub',        'var', 'expr'),
        'Expr_AssignMul'        => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignMul',        'var', 'expr'),
        'Expr_AssignDiv'        => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignDiv',        'var', 'expr'),
        'Expr_AssignMod'        => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignMod',        'var', 'expr'),
        'Expr_AssignConcat'     => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignConcat',     'var', 'expr'),
        'Expr_AssignBitwiseAnd' => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignBitwiseAnd', 'var', 'expr'),
        'Expr_AssignBitwiseOr'  => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignBitwiseOr',  'var', 'expr'),
        'Expr_AssignBitwiseXor' => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignBitwiseXor', 'var', 'expr'),
        'Expr_AssignShiftLeft'  => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignShiftLeft',  'var', 'expr'),
        'Expr_AssignShiftRight' => array('BinaryAssignOp', 'PHPPHP\Engine\OpLines\AssignShiftRight', 'var', 'expr'),

        // binary operators
        'Expr_PropertyFetch'    => array('BinaryOp', 'PHPPHP\Engine\OpLines\ObjectPropertyFetch', 'var', 'name'),
        'Expr_ClassConstFetch'  => array('BinaryOp', 'PHPPHP\Engine\OpLines\ClassConstantFetch', 'class', 'name'),
        'Expr_BooleanAnd'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanAnd'),
        'Expr_BooleanOr'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanOr'),
        'Expr_LogicalAnd'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanAnd'),
        'Expr_LogicalOr'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanOr'),
        'Expr_Smaller'          => array('BinaryOp', 'PHPPHP\Engine\OpLines\Smaller'),
        'Expr_SmallerOrEqual'   => array('BinaryOp', 'PHPPHP\Engine\OpLines\SmallerOrEqual'),
        'Expr_Greater'          => array('BinaryOp', 'PHPPHP\Engine\OpLines\Smaller', 'right', 'left'),
        'Expr_GreaterOrEqual'   => array('BinaryOp', 'PHPPHP\Engine\OpLines\SmallerOrEqual', 'right', 'left'),
        'Expr_Equal'            => array('BinaryOp', 'PHPPHP\Engine\OpLines\Equal'),
        'Expr_NotEqual'         => array('BinaryOp', 'PHPPHP\Engine\OpLines\NotEqual'),
        'Expr_Identical'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\Identical'),
        'Expr_NotIdentical'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\NotIdentical'),
        'Expr_Plus'             => array('BinaryOp', 'PHPPHP\Engine\OpLines\Add'),
        'Expr_Minus'            => array('BinaryOp', 'PHPPHP\Engine\OpLines\Sub'),
        'Expr_Mul'              => array('BinaryOp', 'PHPPHP\Engine\OpLines\Mul'),
        'Expr_Div'              => array('BinaryOp', 'PHPPHP\Engine\OpLines\Div'),
        'Expr_Mod'              => array('BinaryOp', 'PHPPHP\Engine\OpLines\Mod'),
        'Expr_Concat'           => array('BinaryOp', 'PHPPHP\Engine\OpLines\Concat'),
        'Expr_BitwiseAnd'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseAnd'),
        'Expr_BitwiseOr'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseOr'),
        'Expr_BitwiseXor'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseXor'),
        'Expr_ShiftLeft'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\ShiftLeft'),
        'Expr_ShiftRight'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\ShiftRight'),

        'Expr_Include'          => array('BinaryOp', 'PHPPHP\Engine\OpLines\IncludeOp', 'type', 'expr'),
    );

    /** @var OpArray */
    protected $opArray;

    /** @var ClassEntry */
    protected $currentClass;

    protected $fileName = '';
    // Needed because it may be CWD not the dirname of the filename
    protected $currentDir = '';

    protected $functionStore;

    protected $autoGlobals = array(
        'GLOBALS',
        '_GET',
        '_POST',
        '_COOKIE',
        '_SERVER',
        '_ENV',
        '_REQUEST',
        '_FILES',
    );

    protected $fetchWrite = false;

    public function __construct(FunctionStore $functionStore) {
        $this->functionStore = $functionStore;
    }

    public function setFileName($name, $dir) {
        $this->fileName = $name;
        $this->currentDir = $dir;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function compile(array $ast, Zval\Ptr $returnContext = null) {
        $opArray = new OpArray($this->fileName);
        $this->opArray = $opArray;

        $this->compileTopLevelFunctions($ast);
        $this->compileNodes($ast, $returnContext);
        $opArray[] = new OpLines\ReturnOp(end($ast)->getLine());

        unset($this->opArray);
        return $opArray;
    }

    protected function compileTopLevelFunctions(array $ast) {
        foreach ($ast as $node) {
            if ($node instanceof \PHPParser_Node_Stmt_Function) {
                $funcData = $this->compileFunction($node);
                $this->functionStore->register($node->namespacedName, $funcData);
                $node->alreadyCompiled = true;
            } elseif ($node instanceof \PHPParser_Node_Stmt_Namespace) {
                $this->compileTopLevelFunctions($node->stmts);
            }
        }
    }

    protected function compileNodes(array $ast, Zval\Ptr $returnContext = null) {
        foreach ($ast as $node) {
            $this->compileNode($node, $returnContext);
        }
    }

    protected function compileNode(\PHPParser_Node $node, Zval\Ptr $returnContext = null) {
        $nodeType = $node->getType();
        if (isset($this->operators[$nodeType])) {
            call_user_func_array(
                array($this, 'compile' . $this->operators[$nodeType][0]),
                array_merge(array($node, $returnContext), array_slice($this->operators[$nodeType], 1))
            );

            return;
        }

        $methodName = 'compile_' . $nodeType;
        if (!method_exists($this, $methodName)) {
            var_dump($node);
            throw new CompileException($nodeType . ' not supported yet', $node->line);
        }

        call_user_func(array($this, $methodName), $node, $returnContext);
    }

    protected function compileChild(\PHPParser_Node $node, $childName, $returnContext = null) {
        $childNode = $node->$childName;
        if (is_null($childNode)) {
            return;
        }

        if (is_scalar($childNode)) {
            $returnContext->setValue($childNode);
        } elseif (is_array($childNode)) {
            $this->compileNodes($childNode, $returnContext);
        } else {
            $this->compileNode($childNode, $returnContext);
        }
    }

    protected function compileArrayOp($node, $returnContext, $left = 'left') {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, $left, $op1);
        if ($returnContext) {
            if (!$returnContext->isArray()) {
                $returnContext->setValue($returnContext->toArray());
            }
            $array = $returnContext->toArray();
            $array[] = $op1;
            $returnContext->setValue($array);
        }
    }

    protected function compileBinaryOp($node, $returnContext, $class, $left = 'left', $right = 'right') {
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();

        $this->compileChild($node, $left, $op1);
        $this->compileChild($node, $right, $op2);

        $this->opArray[] = new $class($node->getLine(), $op1, $op2, $returnContext ?: Zval::ptrFactory());
    }

    public function compileBinaryAssignOp($node, $returnContext, $class) {
        $property = null;
        $dim = null;
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();

        $prevFetchWrite = $this->fetchWrite;
        $this->fetchWrite = true;

        if ($node->var instanceof \PHPParser_Node_Expr_PropertyFetch) {
            $var = $node->var;
            $property = Zval::ptrFactory();
            $this->compileChild($var, 'var', $op1);
            $this->compileChild($var, 'name', $property);
        } else if ($node->var instanceof \PHPParser_Node_Expr_ArrayDimFetch) {
            $var = $node->var;
            $this->compileChild($var, 'var', $op1);
            if ($var->dim) {
                $dim = Zval::ptrFactory();
                $this->compileChild($var, 'dim', $dim);
            }
        } else {
            $this->compileChild($node, 'var', $op1);
        }

        $this->fetchWrite = $prevFetchWrite;

        $this->compileChild($node, 'expr', $op2);

        $opline = new $class($node->getLine(), $op1, $op2, $returnContext);
        $opline->property = $property;
        $opline->dim = $dim;

        $this->opArray[] = $opline;
    }

    protected function compileUnaryOp($node, $returnContext, $class, $expr = 'expr') {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, $expr, $op1);
        $this->opArray[] = new $class($node->getLine(), $op1, null, $returnContext ?: Zval::ptrFactory());
    }

    protected function compileScalarOp($node, $returnContext, $name = 'value', $sep = '') {
        if ($returnContext) {
            if ($sep) {
                $returnContext->setValue(implode($sep, $node->$name));
            } else {
                $returnContext->setValue($node->$name);
            }
        }
    }

    public function compile_Stmt_Echo($node, $returnContext = null) {
        foreach ($node->exprs as $expr) {
            $exprPtr = Zval::ptrFactory();
            $this->compileNode($expr, $exprPtr);
            $this->opArray[] = new OpLines\EchoOp($node->getLine(), $exprPtr, null, $returnContext ?: Zval::ptrFactory());
        }
    }

    public function compile_Expr_Assign($node, $returnContext = null) {
        if ($node->var instanceof \PHPParser_Node_Expr_ArrayDimFetch) {
            $this->compileBinaryAssignOp($node, $returnContext, 'PHPPHP\Engine\OpLines\AssignDim', 'var', 'expr');
        } else {
            $this->compileBinaryAssignOp($node, $returnContext, 'PHPPHP\Engine\OpLines\Assign', 'var', 'expr');
        }
    }

    protected function compile_Expr_ArrayDimFetch($node, $returnContext = null) {
        $varPtr = Zval::ptrFactory();
        $dimPtr = Zval::ptrFactory();

        $this->compileChild($node, 'var', $varPtr);
        $this->compileChild($node, 'dim', $dimPtr);

        $opLine = new OpLines\ArrayDimFetch($node->getLine(), $varPtr, $dimPtr, $returnContext ?: Zval::ptrFactory());
        $opLine->write = $this->fetchWrite;

        $this->opArray[] = $opLine;
    }

    protected function compile_Expr_Array($node, $returnContext = null) {
        if ($returnContext) {
            $returnContext->setValue($returnContext->toArray());
            foreach ($node->items as $subNode) {
                $this->compileNode($subNode, $returnContext);
            }
        }
    }

    protected function compile_Expr_ArrayItem($node, $returnContext = null) {
        if (!$returnContext) return;

        $keyPtr = Zval::ptrFactory();
        $this->compileChild($node, 'key', $keyPtr);

        $valuePtr = Zval::ptrFactory();
        $this->compileChild($node, 'value', $valuePtr);

        if ($node->byRef) {
            $this->opArray[] = new OpLines\AddArrayElementRef($node->getLine(), $keyPtr, $valuePtr, $returnContext);
        } else {
            $this->opArray[] = new OpLines\AddArrayElement($node->getLine(), $keyPtr, $valuePtr, $returnContext);
        }
    }

    protected function compile_Expr_Closure($node, $returnContext = null) {
        $funcData = $this->compileFunction($node);
        $this->opArray[] = new OpLines\ClosureDef($node->getLine(), Zval::factory($node->namespacedName), $funcData, $returnContext);
    }

    protected function compile_Expr_ErrorSuppress($node, $returnContext = null) {
        // Place holder for opcode to turn on suppression
        $this->opArray[] = new OpLines\NoOp($node->getLine());
        $this->compileChild($node, 'expr', $returnContext);
        // Place holder for opcode to turn off suppression
        $this->opArray[] = new OpLines\NoOp($node->getLine());
    }

    protected function compile_Expr_FuncCall($node, $returnContext = null) {
        $namePtr = Zval::ptrFactory();
        $args = array();

        $this->compileChild($node, 'name', $namePtr);
        foreach ($node->args as $arg) {
            $ptr = Zval::ptrFactory();
            $this->compileChild($arg, 'value', $ptr);
            $args[] = $ptr;
        }
        $this->opArray[] = new OpLines\InitFCallByName($node->getLine(), null, $namePtr);

        foreach ($args as $key => $arg) {
            $this->opArray[] = new OpLines\Send($node->getLine(), $arg, Zval::factory($key));
        }

        $this->opArray[] = new OpLines\FunctionCall($node->getLine(), null, null, $returnContext ?: Zval::ptrFactory());;
    }

    protected function compile_Expr_MethodCall($node, $returnContext = null) {
        $varPtr = Zval::ptrFactory();
        $namePtr = Zval::ptrFactory();
        $args = array();
        $this->compileChild($node, 'var', $varPtr);
        $this->compileChild($node, 'name', $namePtr);
        foreach ($node->args as $arg) {
            $ptr = Zval::ptrFactory();
            $this->compileChild($arg, 'value', $ptr);
            $args[] = $ptr;
        }
        $this->opArray[] = new OpLines\InitFCallByName($node->getLine(), $varPtr, $namePtr);

        foreach ($args as $key => $arg) {
            $this->opArray[] = new OpLines\Send($node->getLine(), $arg, Zval::factory($key));
        }

        $this->opArray[] = new OpLines\FunctionCall($node->getLine(), null, null, $returnContext ?: Zval::ptrFactory());;
    }

    protected function compile_Expr_StaticCall($node, $returnContext = null) {
        $classPtr = Zval::ptrFactory();
        $namePtr = Zval::ptrFactory();
        $args = array();
        $this->compileChild($node, 'class', $classPtr);
        $this->compileChild($node, 'name', $namePtr);
        foreach ($node->args as $arg) {
            $ptr = Zval::ptrFactory();
            $this->compileChild($arg, 'value', $ptr);
            $args[] = $ptr;
        }
        $this->opArray[] = new OpLines\InitStaticMethodCall($node->getLine(), $classPtr, $namePtr);

        foreach ($args as $key => $arg) {
            $this->opArray[] = new OpLines\Send($node->getLine(), $arg, Zval::factory($key));
        }

        $this->opArray[] = new OpLines\FunctionCall($node->getLine(), null, null, $returnContext ?: Zval::ptrFactory());;
    }

    protected function compile_Expr_List($node, $returnContext = null) {
        if ($returnContext) {
            $vars = array();
            foreach ($node->vars as $subNode) {
                if ($subNode) {
                    $ret = Zval::ptrFactory();
                    $vars[] = $ret;
                    $this->compileNode($subNode, $ret);
                } else {
                    $vars[] = null;
                }
            }
            $listPtr = new Zval\VariableList($vars);
            $returnContext->forceValue($listPtr);
        }
    }

    protected function compile_Expr_ShellExec($node, $returnContext = null) {
        $returnContext = $returnContext ?: Zval::ptrFactory();
        $lineContext = Zval::ptrFactory();

        foreach ($node->parts as $part) {
            if (is_string($part)) {
                $this->opArray[] = new OpLines\AssignConcat($node->getLine(), $lineContext, Zval::ptrFactory($part));
            } else {
                $ret = Zval::ptrFactory();
                $this->compileNode($part, $ret);
                $this->opArray[] = new OpLines\AssignConcat($node->getLine(), $lineContext, $ret);
            }
        }
        $this->opArray[] = new OpLines\ShellExec($node->getLine(), null, null, $returnContext);
    }

    protected function compile_Expr_Ternary($node, $returnContext = null) {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        $this->opArray[] = $midJumpOp = new OpLines\JumpIfNot($node->getLine(), $op1);

        $ifAssign = Zval::ptrFactory();
        $this->compileChild($node, 'if', $ifAssign);
        $this->opArray[] = new OpLines\Assign($node->getLine(), $returnContext, $ifAssign);
        $this->opArray[] = $endJumpOp = new OpLines\Jump($node->getLine());

        $midJumpOp->op2 = $this->opArray->getNextOffset();
        $elseAssign = Zval::ptrFactory();
        $this->compileChild($node, 'else', $elseAssign);
        $this->opArray[] = new OpLines\Assign($node->getLine(), $returnContext, $elseAssign);
        $endJumpOp->op1 = $this->opArray->getNextOffset();
    }

    protected function compile_Expr_Variable($node, $returnContext) {
        $name = Zval::ptrFactory();
        $this->compileChild($node, 'name', $name);
        $scope = Variable::SCOPE_LOCAL;
        if (is_string($node->name) && in_array($name->getValue(), $this->autoGlobals)) {
            $scope = Variable::SCOPE_GLOBAL;
        }
        $variable = Zval::variableFactory($name, null, $scope);
        $this->opArray->addCompiledVariable($variable);
        $returnContext->assignZval($variable);
    }

    protected function compile_Expr_StaticPropertyFetch($node, $returnContext) {
        $class = Zval::ptrFactory();
        $name = Zval::ptrFactory();
        $this->compileChild($node, 'class', $class);
        $this->compileChild($node, 'name', $name);
        $variable = Zval::variableFactory($name, $class);
        $this->opArray->addCompiledVariable($variable);
        $returnContext->assignZval($variable);
    }

    protected function compile_Name_FullyQualified($node, $returnContext) {
        if ($returnContext) {
            $returnContext->setValue(implode('\\', $node->parts));
        }
    }

    protected function compile_Scalar_Encapsed($node, $returnContext = null) {
        $returnContext = $returnContext ?: Zval::ptrFactory();
        $this->opArray[] = new OpLines\Assign($node->getLine(), $returnContext, Zval::ptrFactory(''));
        foreach ($node->parts as $part) {
            if (is_string($part)) {
                $this->opArray[] = new OpLines\AssignConcat($node->getLine(), $returnContext, Zval::ptrFactory($part));
            } else {
                $ret = Zval::ptrFactory();
                $this->compileNode($part, $ret);
                $this->opArray[] = new OpLines\AssignConcat($node->getLine(), $returnContext, $ret);
            }
        }
    }

    protected function compile_Scalar_DirConst($node, $returnContext = null) {
        if ($returnContext) {
            $returnContext->setValue($this->currentDir);
        }
    }

    protected function compile_Scalar_FileConst($node, $returnContext = null) {
        if ($returnContext) {
            $returnContext->setValue($this->fileName);
        }
    }

    protected function getBreakContinueLevel($node, $type) {
        if ($node->num === null) {
            return 1;
        }

        if (!$node->num instanceof \PHPParser_Node_Scalar_LNumber || $node->num->value < 1) {
            throw new CompileException("'$type' operator accepts only positive numbers", $node->getLine());
        }

        return $node->num->value;
    }

    protected function compile_Stmt_Break($node) {
        $this->opArray[] = new OpLines\BreakOp(
            $node->getLine(),
            $this->opArray->getBreakContinueInfoAtLevel($this->getBreakContinueLevel($node, "break"))
        );
    }

    protected function compile_Stmt_Continue($node) {
        $this->opArray[] = new OpLines\ContinueOp(
            $node->getLine(),
            $this->opArray->getBreakContinueInfoAtLevel($this->getBreakContinueLevel($node, "continue"))
        );
    }

    protected function compile_Stmt_For($node) {
        $this->compileChild($node, 'init');

        $this->opArray->beginLoop();
        $startJumpPos = $this->opArray->getNextOffset();
        $condPtr = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $condPtr);
        $this->opArray[] = $endJumpOp = new OpLines\JumpIfNot($node->getLine(), $condPtr);
        $this->compileChild($node, 'stmts');
        $continueJumpPos = $this->opArray->getNextOffset();
        $this->compileChild($node, 'loop');
        $this->opArray[] = new OpLines\Jump($node->getLine(), $startJumpPos);
        $endJumpOp->op2 = $this->opArray->getNextOffset();
        $this->opArray->endLoop($continueJumpPos);
    }

    protected function compile_Stmt_Foreach($node) {
        $iteratePtr = Zval::ptrFactory();

        $this->compileChild($node, 'expr', $iteratePtr);

        $this->opArray->beginLoop();

        $key = null;
        if ($node->keyVar) {
            $key = Zval::ptrFactory();
            $this->compileChild($node, 'keyVar', $key);
        }
        $value = Zval::ptrFactory();
        $this->compileChild($node, 'valueVar', $value);

        $iterator = Zval::iteratorFactory();

        if ($node->byRef) {
            $this->opArray[] = $iterateOp = new OpLines\IterateByRef($node->getLine(), $iteratePtr, null, $iterator);

            $iterateValuesJumpPos = $this->opArray->getNextOffset();
            $this->opArray[] = new OpLines\IterateValuesByRef($node->getLine(), $iterator, $key, $value);
        } else {
            $this->opArray[] = $iterateOp = new OpLines\Iterate($node->getLine(), $iteratePtr, null, $iterator);

            $iterateValuesJumpPos = $this->opArray->getNextOffset();
            $this->opArray[] = new OpLines\IterateValues($node->getLine(), $iterator, $key, $value);
        }

        $this->compileChild($node, 'stmts');

        $continueJumpPos = $this->opArray->getNextOffset();
        $this->opArray[] = new OpLines\IterateNext($node->getLine(), $iterator, $iterateValuesJumpPos);

        $iterateOp->op2 = $this->opArray->getNextOffset();
        $this->opArray->endLoop($continueJumpPos);
    }

    protected function compile_Stmt_Function(\PHPParser_Node_Stmt_Function $node) {
        if ($node->alreadyCompiled) return;

        $funcData = $this->compileFunction($node);
        $this->opArray[] = new OpLines\FunctionDef($node->getLine(), Zval::factory($node->namespacedName), $funcData);
    }

    protected function compile_Stmt_Global($node) {
        foreach ($node->vars as $var) {
            $varName = (string) $var->name;
            $this->opArray[] = new OpLines\FetchGlobalVariable($node->getLine(), Zval::ptrFactory($varName));
        }
    }

    protected function compile_Stmt_If($node) {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        $endJumpOps = array();

        $this->opArray[] = $midJumpOp = new OpLines\JumpIfNot($node->getLine(), $op1);

        $this->compileChild($node, 'stmts');

        $this->opArray[] = $endJumpOps[] = new OpLines\Jump($node->getLine());

        $midJumpOp->op2 = $this->opArray->getNextOffset();

        $elseifs = $node->elseifs;
        foreach ($elseifs as $child) {
            $op1 = Zval::ptrFactory();
            $this->compileChild($child, 'cond', $op1);

            $this->opArray[] = $midJumpOp = new OpLines\JumpIfNot($node->getLine(), $op1);
            $this->compileChild($child, 'stmts');
            $this->opArray[] = $endJumpOps[] = new OpLines\Jump($node->getLine());
            $midJumpOp->op2 = $this->opArray->getNextOffset();
        }

        if ($node->else) {
            $this->compileChild($node->else, 'stmts');
        }

        foreach ($endJumpOps as $endJumpOp) {
            $endJumpOp->op1 = $this->opArray->getNextOffset();
        }
    }

    protected function compile_Stmt_Static($node) {
        $this->compileChild($node, 'vars');
    }

    protected function compile_Stmt_StaticVar($node) {
        $varName = Zval::ptrFactory();
        $this->compileChild($node, 'name', $varName);
        $varValue = null;
        if ($node->default) {
            $varValue = Zval::ptrFactory();
            $this->compileChild($node, 'default', $varValue);
        }
        $this->opArray[] = new OpLines\StaticAssign($node->getLine(), $varName, $varValue);
    }

    protected function compile_Stmt_Switch($node) {
        $condPtr = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $condPtr);

        $this->opArray->beginLoop();

        foreach ($node->cases as $case) {
            if ($case->cond) {
                $comparePtr = Zval::ptrFactory();
                $this->compileChild($case, 'cond', $comparePtr);
                $conditionPtr = Zval::ptrFactory();
                $this->opArray[] = new OpLines\Equal($node->getLine(), $condPtr, $comparePtr, $conditionPtr);
                $this->opArray[] = $caseEndJumpOp = new OpLines\JumpIfNot($node->getLine(), $conditionPtr);
                $this->compileChild($case, 'stmts');
                $caseEndJumpOp->op2 = $this->opArray->getNextOffset();
            } else {
                // Default case
                $this->compileChild($case, 'stmts');
            }
        }

        $this->opArray->endLoop($this->opArray->getNextOffset());
    }

    protected function compile_Stmt_While($node) {
        $this->opArray->beginLoop();
        $startJumpPos = $this->opArray->getNextOffset();

        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        $this->opArray[] = $endJumpOp = new OpLines\JumpIfNot($node->getLine(), $op1);
        $this->compileChild($node, 'stmts');
        $this->opArray[] = new OpLines\Jump($node->getLine(), $startJumpPos);

        $endJumpOp->op2 = $this->opArray->getNextOffset();
        $this->opArray->endLoop($startJumpPos);
    }

    protected function compile_Stmt_Do($node) {
        $op1 = Zval::ptrFactory();

        $this->opArray->beginLoop();
        $startJumpPos = $this->opArray->getNextOffset();
        $this->compileChild($node, 'stmts');
        $continueJumpPos = $this->opArray->getNextOffset();
        $this->compileChild($node, 'cond', $op1);
        $this->opArray[] = new OpLines\JumpIf($node->getLine(), $op1, $startJumpPos);
        $this->opArray->endLoop($continueJumpPos);
    }

    protected function compile_Stmt_InlineHtml($node) {
        $this->opArray[] = new OpLines\EchoOp($node->getLine(), Zval::ptrFactory($node->value));
    }

    protected function compile_Stmt_Class($node) {
        $class = new ClassEntry($node->name);
        $this->currentClass = $class;
        $this->compileChild($node, 'stmts');
        $this->currentClass = null;
        $this->opArray[] = new OpLines\ClassDef($node->getLine(), $class);
    }

    protected function compile_Stmt_Property($node) {
        foreach ($node->props as $prop) {
            $name = $prop->name;
            $default = Zval::ptrFactory();
            $this->compileChild($prop, 'default', $default);
            $acc = $this->getPropertyAccess($node);
            $this->currentClass->declareProperty($name, $default, $acc);
        }
    }

    protected function getPropertyAccess($node) {
        $acc = 0;
        if ($node->isPublic()) {
            $acc |= Scope::ACC_PUBLIC;
        }
        if ($node->isProtected()) {
            $acc |= Scope::ACC_PROTECTED;
        }
        if ($node->isPrivate()) {
            $acc |= Scope::ACC_PRIVATE;
        }
        if ($node->isStatic()) {
            $acc |= Scope::ACC_STATIC;
        }
        return $acc;
    }

    public function compile_Stmt_ClassConst($node) {
        foreach ($node->consts as $const) {
            $name = $const->name;
            $value = Zval::ptrFactory();
            $this->compileChild($const, 'value', $value);
            $this->currentClass->defineConstant($name, $value);
        }
    }

    protected function compile_Stmt_ClassMethod($node) {
        $funcData = $this->compileFunction($node);
        $this->currentClass->getMethodStore()->register($node->name, $funcData);
    }

    public function compile_Expr_New($node, $returnContext = null) {

        $args = array();

        foreach ($node->args as $arg) {
            $ptr = Zval::ptrFactory();
            $this->compileChild($arg, 'value', $ptr);
            $args[] = $ptr;
        }

        if (!$returnContext) {
            $returnContext = Zval::ptrFactory();
        }

        $this->opArray[] = $newOp = new OpLines\NewOp($node->getLine(), Zval::ptrFactory($node->class->toString()), null, $returnContext);

        foreach ($args as $key => $arg) {
            $this->opArray[] = new OpLines\Send($node->getLine(), $arg, Zval::factory($key));
        }

        $this->opArray[] = new OpLines\FunctionCall($node->getLine(), null, $returnContext, Zval::ptrFactory());

        $newOp->noConstructorJumpOffset = $this->opArray->getNextOffset();
    }

    protected function compileFunction($node) {
        $prevOpArray = $this->opArray;
        $this->opArray = new OpArray($this->fileName);

        $params = array();

        foreach ($node->params as $i => $param) {
            $type = null;
            if ($param->type && $param->type instanceof \PHPParser_Node) {
                $tmpZval = Zval::ptrFactory();
                $this->compileChild($param, 'type', $tmpZval);
                $type = $tmpZval->toString();
            } elseif (is_string($param->type)) {
                $type = $param->type;
            }
            $params[] = new ParamData($param->name, $param->byRef, $type, (bool) $param->default, $node->getLine());

            if ($param->default) {
                $default = Zval::ptrFactory();
                $this->compileChild($param, 'default', $default);
                if ($param->type == 'array' && !($default->isArray() || $default->isNull())) {
                    throw new CompileException('Default value for parameters with array type hint can only be an array or NULL', $node->getLine());
                } elseif ($param->type != 'array' && $param->type && !$default->isNull()) {
                    throw new CompileException('Default value for parameters with a class type hint can only be NULL', $node->getLine());
                }
                $this->opArray[] = new OpLines\RecvInit($node->getLine(), Zval::factory($i), $default);
            } else {
                $this->opArray[] = new OpLines\Recv($node->getLine(), Zval::factory($i));
            }
        }

        $this->compileChild($node, 'stmts');

        $this->opArray[] = new OpLines\ReturnOp($node->getLine());

        $funcData = new FunctionData\User($this->opArray, (bool) $node->byRef, $params);
        $this->opArray = $prevOpArray;
        return $funcData;
    }

    protected function makeZvalFromNodeStrict(\PHPParser_Node $node) {
        $zval = $this->makeZvalFromNode($node);

        if (null === $zval) {
            throw new CompileException('Cannot evaluate non-constant expression at compile time', $node->getLine());
        }

        return $zval;
    }

    protected function makeZvalFromNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Scalar_LNumber
            || $node instanceof \PHPParser_Node_Scalar_DNumber
            || $node instanceof \PHPParser_Node_Scalar_String
        ) {
            return Zval::factory($node->value);
        } elseif ($node instanceof \PHPParser_Node_Expr_Array) {
            $array = array();
            foreach ($node->items as $item) {
                if ($item->byRef) {
                    return null;
                }

                $array[$this->makeZvalFromNode($item->key)] = $this->makeZvalFromNode($item->value);
            }
            return $array;
        } elseif ($node instanceof \PHPParser_Node_Scalar_FileConst /* ... */) {
            /* TODO */
            return null;
        } else {
            return null;
        }
    }
}
