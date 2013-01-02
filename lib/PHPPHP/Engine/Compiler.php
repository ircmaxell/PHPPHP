<?php

namespace PHPPHP\Engine;

class Compiler {

    protected $operators = array(
        'Arg' => array('ArrayOp', 'value'),

        // scalars
        'Name'           => array('ScalarOp', 'parts', '\\'),
        'Scalar_DNumber' => array('ScalarOp'),
        'Scalar_LNumber' => array('ScalarOp'),
        'Scalar_String'  => array('ScalarOp'),

        // unary operators
        'Expr_Eval'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\EvalOp', 'expr'),
        'Expr_Exit'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\ExitOp', 'expr'),
        'Expr_BooleanNot' => array('UnaryOp', 'PHPPHP\Engine\OpLines\BooleanNot'),
        'Expr_BitwiseNot' => array('UnaryOp', 'PHPPHP\Engine\OpLines\BitwiseNot'),
        'Expr_Isset'      => array('UnaryOp', 'PHPPHP\Engine\OpLines\IssetOp', 'vars'),
        'Expr_PostDec'    => array('UnaryOp', 'PHPPHP\Engine\OpLines\PostDec', 'var'),
        'Expr_PostInc'    => array('UnaryOp', 'PHPPHP\Engine\OpLines\PostInc', 'var'),
        'Expr_PreDec'     => array('UnaryOp', 'PHPPHP\Engine\OpLines\PreDec', 'var'),
        'Expr_PreInc'     => array('UnaryOp', 'PHPPHP\Engine\OpLines\PreInc', 'var'),
        'Expr_UnaryPlus'  => array('UnaryOp', 'PHPPHP\Engine\OpLines\UnaryPlus', 'expr'),
        'Expr_UnaryMinus' => array('UnaryOp', 'PHPPHP\Engine\OpLines\UnaryMinus', 'expr'),
        'Expr_ConstFetch' => array('UnaryOp', 'PHPPHP\Engine\OpLines\FetchConstant', 'name'),
        'Stmt_Echo'       => array('UnaryOp', 'PHPPHP\Engine\OpLines\EchoOp', 'exprs'),
        'Stmt_Return'     => array('UnaryOp', 'PHPPHP\Engine\OpLines\ReturnOp'),

        // binary operators
        'Expr_ArrayDimFetch'  => array('BinaryOp', 'PHPPHP\Engine\OpLines\ArrayDimFetch', 'var', 'dim'),
        'Expr_Assign'         => array('BinaryOp', 'PHPPHP\Engine\OpLines\Assign', 'var', 'expr'),
        'Expr_AssignConcat'   => array('BinaryOp', 'PHPPHP\Engine\OpLines\AssignConcat', 'var', 'expr'),
        'Expr_AssignMul'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\AssignMul', 'var', 'expr'),
        'Expr_AssignPlus'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\AssignPlus', 'var', 'expr'),
        'Expr_AssignRef'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\AssignRef', 'var', 'expr'),
        'Expr_BooleanAnd'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanAnd'),
        'Expr_BooleanOr'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\BooleanOr'),
        'Expr_Smaller'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\Smaller'),
        'Expr_SmallerOrEqual' => array('BinaryOp', 'PHPPHP\Engine\OpLines\SmallerOrEqual'),
        'Expr_Greater'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\Smaller', 'right', 'left'),
        'Expr_GreaterOrEqual' => array('BinaryOp', 'PHPPHP\Engine\OpLines\SmallerOrEqual', 'right', 'left'),
        'Expr_Equal'          => array('BinaryOp', 'PHPPHP\Engine\OpLines\Equal'),
        'Expr_NotEqual'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\NotEqual'),
        'Expr_Identical'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\Identical'),
        'Expr_NotIdentical'   => array('BinaryOp', 'PHPPHP\Engine\OpLines\NotIdentical'),
        'Expr_Plus'           => array('BinaryOp', 'PHPPHP\Engine\OpLines\Add'),
        'Expr_Minus'          => array('BinaryOp', 'PHPPHP\Engine\OpLines\Sub'),
        'Expr_Mul'            => array('BinaryOp', 'PHPPHP\Engine\OpLines\Multiply'),
        'Expr_Div'            => array('BinaryOp', 'PHPPHP\Engine\OpLines\Div'),
        'Expr_Mod'            => array('BinaryOp', 'PHPPHP\Engine\OpLines\Mod'),
        'Expr_Concat'         => array('BinaryOp', 'PHPPHP\Engine\OpLines\Concat'),
        'Expr_BitwiseAnd'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseAnd'),
        'Expr_BitwiseOr'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseOr'),
        'Expr_BitwiseXor'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\BitwiseXor'),
        'Expr_ShiftLeft'      => array('BinaryOp', 'PHPPHP\Engine\OpLines\ShiftLeft'),
        'Expr_ShiftRight'     => array('BinaryOp', 'PHPPHP\Engine\OpLines\ShiftRight'),

        'Expr_FuncCall'       => array('BinaryOp', 'PHPPHP\Engine\OpLines\FunctionCall', 'name', 'args'),
        'Expr_Include'        => array('BinaryOp', 'PHPPHP\Engine\OpLines\IncludeOp', 'type', 'expr'),
    );

    /** @var OpArray */
    protected $opArray;

    public function compile(array $ast, Zval\Ptr $returnContext = null) {
        $opArray = new OpArray;

        $this->opArray = $opArray;
        $this->compileNodes($ast, $returnContext);
        unset($this->opArray);

        $opArray[] = new OpLines\ReturnOp();

        return $opArray;
    }

    public function compileNodes(array $ast, Zval\Ptr $returnContext = null) {
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
            throw new \Exception($nodeType . ' not supported yet');
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
            $returnContext->getHashTable()->append($op1);
        }
    }

    protected function compileBinaryOp($node, $returnContext, $class, $left = 'left', $right = 'right') {
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();

        $this->compileChild($node, $left, $op1);
        $this->compileChild($node, $right, $op2);

        $this->opArray[] = new $class($op1, $op2, $returnContext ?: Zval::ptrFactory());
    }

    protected function compileUnaryOp($node, $returnContext, $class, $expr = 'expr') {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, $expr, $op1);
        $this->opArray[] = new $class($op1, null, $returnContext ?: Zval::ptrFactory());
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

        $this->opArray[] = new OpLines\AddArrayElement($keyPtr, $valuePtr, $returnContext);
    }

    protected function compile_Expr_Ternary($node, $returnContext = null) {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        // Jump targets: midOp is after the first if branch, endOp is after all branches
        $midOp = new OpLines\NoOp;
        $endOp = new OpLines\NoOp;

        $this->opArray[] = $ops[] = new OpLines\JumpIfNot($op1, $midOp);

        $ifAssign = Zval::ptrFactory();
        $this->compileChild($node, 'if', $ifAssign);
        $this->opArray[] = new OpLines\Assign($returnContext, $ifAssign);
        $this->opArray[] = new OpLines\JumpTo($endOp);

        $this->opArray[] = $midOp;
        $elseAssign = Zval::ptrFactory();
        $this->compileChild($node, 'else', $elseAssign);
        $this->opArray[] = new OpLines\Assign($returnContext, $elseAssign);
        $this->opArray[] = $endOp;
    }

    protected function compile_Expr_Variable($node, $returnContext) {
        $name = Zval::ptrFactory();
        $this->compileChild($node, 'name', $name);
        $variable = Zval::variableFactory($name);
        $this->opArray->addCompiledVariable($variable);
        $returnContext->assignZval($variable);
    }

    protected function compile_Scalar_Encapsed($node, $returnContext = null) {
        $returnContext = $returnContext ?: Zval::ptrFactory();
        $this->opArray[] = new OpLines\Assign($returnContext, Zval::ptrFactory(''));
        foreach ($node->parts as $part) {
            if (is_string($part)) {
                $this->opArray[] = new OpLines\AssignConcat($returnContext, Zval::ptrFactory($part));
            } else {
                $ret = Zval::ptrFactory();
                $this->compileNode($part, $ret);
                $this->opArray[] = new OpLines\AssignConcat($returnContext, $ret);
            }
        }
    }

    protected function compile_Stmt_Break($node) {
        $op1 = null;
        if ($node->num) {
            $op1 = Zval::ptrFactory();
            $this->compileChild($node, 'num', $op1);
        }
        $this->opArray[] = new OpLines\BreakOp($op1);
    }

    protected function compile_Stmt_For($node) {
        $this->compileChild($node, 'init');
        $startOp = new OpLines\NoOp;
        $endOp = new OpLines\StatementStackPop;
        $this->opArray[] = new OpLines\StatementStackPush($endOp);
        $this->opArray[] = $startOp;
        $condPtr = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $condPtr);
        $this->opArray[] = new OpLines\JumpIfNot($condPtr, $endOp);
        $this->compileChild($node, 'stmts');
        $this->compileChild($node, 'loop');
        $this->opArray[] = new OpLines\JumpTo($startOp);
        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_Foreach($node) {
        $iteratePtr = Zval::ptrFactory();

        $this->compileChild($node, 'expr', $iteratePtr);
        $endOp = new OpLines\StatementStackPop;
        $this->opArray[] = new OpLines\StatementStackPush($endOp);

        $key = null;
        if ($node->keyVar) {
            $key = Zval::ptrFactory();
            $this->compileChild($node, 'keyVar', $key);
        }
        $value = Zval::ptrFactory();
        $this->compileChild($node, 'valueVar', $value);

        $iterator = Zval::iteratorFactory();

        $this->opArray[] = new OpLines\Iterate($iteratePtr, $endOp, $iterator);

        $iterateValues = new OpLines\IterateValues($iterator, $key, $value);
        $this->opArray[] = $iterateValues;

        $this->compileChild($node, 'stmts');

        $this->opArray[] = new OpLines\IterateNext($iterator, $iterateValues);
        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_Function(\PHPParser_Node_Stmt_Function $node) {
        $prevOpArray = $this->opArray;
        $this->opArray = new OpArray;

        foreach ($node->params as $i => $param) {
            $arg = Zval::ptrFactory();

            if ($param->default) {
                $this->opArray[] = new OpLines\RecvInit(
                    Zval::factory($i), $this->makeZvalFromNode($param->default), $arg
                );
            } else {
                $this->opArray[] = new OpLines\Recv(Zval::factory($i), null, $arg);
            }

            $var = Zval::variableFactory(Zval::factory($param->name));
            $this->opArray->addCompiledVariable($var);
            $this->opArray[] = new OpLines\Assign($var, $arg);
        }

        $this->compileChild($node, 'stmts');

        $this->opArray[] = new OpLines\ReturnOp;
        $prevOpArray[] = new OpLines\FunctionDef(Zval::factory($node->name), $this->opArray);

        $this->opArray = $prevOpArray;
    }

    protected function compile_Stmt_Global($node) {
        foreach ($node->vars as $var) {
            $varName = (string) $var->name;
            $this->opArray[] = new OpLines\FetchGlobalVariable(Zval::ptrFactory($varName));
        }
    }

    protected function compile_Stmt_If($node) {
        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        // Jump targets: midOp is after the first if branch, endOp is after all branches
        $midOp = new OpLines\NoOp;
        $endOp = new OpLines\NoOp;

        $this->opArray[] = new OpLines\JumpIfNot($op1, $midOp);

        $this->compileChild($node, 'stmts');

        $this->opArray[] = new OpLines\JumpTo($endOp);

        $this->opArray[] = $midOp;

        $elseifs = $node->elseifs;
        foreach ($elseifs as $child) {
            $op1 = Zval::ptrFactory();
            $this->compileChild($child, 'cond', $op1);

            $midOp = new OpLines\NoOp;
            $this->opArray[] = $ops[] = new OpLines\JumpIfNot($op1, $midOp);
            $this->compileChild($child, 'stmts');
            $this->opArray[] = new OpLines\JumpTo($endOp);
            $this->opArray[] = $midOp;
        }

        if ($node->else) {
            $this->compileChild($node->else, 'stmts');
        }

        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_Static($node) {
        $endOp = new OpLines\NoOp;
        $this->opArray[] = new OpLines\StaticOp($endOp);
        $this->compileChild($node, 'vars');
        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_StaticVar($node) {
        $varName = Zval::ptrFactory();
        $this->compileChild($node, 'name', $varName);
        $varValue = Zval::ptrFactory();
        if ($node->default) {
            $this->compileChild($node, 'default', $varValue);
        }
        $this->opArray[] = new OpLines\StaticAssign($varName, $varValue);
    }

    protected function compile_Stmt_Switch($node) {
        $condPtr = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $condPtr);

        $endOp = new OpLines\StatementStackPop;

        $this->opArray[] = new OpLines\StatementStackPush($endOp);

        foreach ($node->cases as $case) {
            if ($case->cond) {
                $comparePtr = Zval::ptrFactory();
                $this->compileChild($case, 'cond', $comparePtr);
                $conditionPtr = Zval::ptrFactory();
                $caseEnd = new OpLines\NoOp;
                $this->opArray[] = new OpLines\Equal($condPtr, $comparePtr, $conditionPtr);
                $this->opArray[] = new OpLines\JumpIfNot($conditionPtr, $caseEnd);
                $this->compileChild($case, 'stmts');
                $this->opArray[] = $caseEnd;
            } else {
                // Default case
                $this->compileChild($case, 'stmts');
            }
        }

        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_While($node) {
        $startJumpPos = $this->opArray->getNextOffset();

        $op1 = Zval::ptrFactory();
        $this->compileChild($node, 'cond', $op1);

        $endOp = new OpLines\StatementStackPop;
        $this->opArray[] = new OpLines\StatementStackPush($endOp);
        $this->opArray[] = new OpLines\JumpIfNot($op1, $endOp);

        $this->compileChild($node, 'stmts');

        // jump back to cond
        $this->opArray[] = new OpLines\Jump($startJumpPos);
        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_Do($node) {
        $op1 = Zval::ptrFactory();

        $endOp = new OpLines\StatementStackPop;
        $this->opArray[] = new OpLines\StatementStackPush($endOp);
        $startOp = new OpLines\NoOp;
        $this->opArray[] = $startOp;
        $this->compileChild($node, 'stmts');
        $this->compileChild($node, 'cond', $op1);
        $this->opArray[] = new OpLines\JumpIf($op1, $startOp);
        $this->opArray[] = $endOp;
    }

    protected function compile_Stmt_InlineHtml($node) {
        $this->opArray[] = new OpLines\EchoOp(Zval::ptrFactory($node->value));
    }

    protected function makeZvalFromNodeStrict(\PHPParser_Node $node) {
        $zval = $this->makeZvalFromNode($node);

        if (null === $zval) {
            throw new \Exception('Cannot evaluate non-constant expression at compile time');
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