<?php

namespace PHPPHP\Engine;

class Compiler {

    protected $operators = array(
        'Arg' => array(
            '', 'ArrayOp',
            'value',
        ),
        'Expr_Assign' => array(
            'PHPPHP\Engine\OpLines\Assign', 'BinaryOp',
            'var', 'expr',
        ),
        'Expr_AssignConcat' => array(
            'PHPPHP\Engine\OpLines\AssignConcat', 'BinaryOp',
            'var', 'expr',
        ),
        'Expr_BooleanAnd'     => array('PHPPHP\Engine\OpLines\BooleanAnd', 'BinaryOp'),
        'Expr_BooleanOr'      => array('PHPPHP\Engine\OpLines\BooleanOr', 'BinaryOp'),
        'Expr_Concat'         => array('PHPPHP\Engine\OpLines\Concat', 'BinaryOp'),
        'Expr_Smaller'        => array('PHPPHP\Engine\OpLines\Smaller', 'BinaryOp'),
        'Expr_SmallerOrEqual' => array('PHPPHP\Engine\OpLines\SmallerOrEqual', 'BinaryOp'),
        'Expr_Greater' => array(
            'PHPPHP\Engine\OpLines\Smaller', 'BinaryOp',
            'right', 'left'
        ),
        'Expr_GreaterOrEqual' => array(
            'PHPPHP\Engine\OpLines\SmallerOrEqual', 'BinaryOp',
            'right', 'left',
        ),
        'Expr_Equal'        => array('PHPPHP\Engine\OpLines\Equal', 'BinaryOp'),
        'Expr_NotEqual'     => array('PHPPHP\Engine\OpLines\NotEqual', 'BinaryOp'),
        'Expr_Identical'    => array('PHPPHP\Engine\OpLines\Identical', 'BinaryOp'),
        'Expr_NotIdentical' => array('PHPPHP\Engine\OpLines\NotIdentical', 'BinaryOp'),
        'Expr_ConstFetch' => array(
            'PHPPHP\Engine\OpLines\FetchConstant', 'UnaryOp',
            'name',
        ),
        'Expr_FuncCall' => array(
            'PHPPHP\Engine\OpLines\FunctionCall', 'BinaryOp',
            'name', 'args'
        ),
        'Expr_Include' => array(
            'PHPPHP\Engine\OpLines\IncludeOp', 'BinaryOp',
            'type', 'expr'
        ),
        'Expr_Isset' => array(
            'PHPPHP\Engine\OpLines\IssetOp', 'UnaryOp',
            'vars',
        ),
        'Expr_Mul' => array('PHPPHP\Engine\OpLines\Multiply', 'BinaryOp'),
        'Expr_Plus' => array('PHPPHP\Engine\OpLines\Add', 'BinaryOp'),
        'Expr_PostInc' => array(
            'PHPPHP\Engine\OpLines\PostInc', 'UnaryOp',
            'var',
        ),
        'Expr_Variable' => array(
            'PHPPHP\Engine\OpLines\FetchVariable', 'UnaryOp',
            'name',
        ),
        'Expr_FetchConstant' => array(
            'PHPPHP\Engine\OpLines\FetchConstant', 'UnaryOp',
            'name'
        ),
        'Name' => array(
            '', 'ScalarOp',
            'parts', '\\',
        ),
        'Scalar_DNumber' => array('', 'ScalarOp'),
        'Scalar_LNumber' => array('', 'ScalarOp'),
        'Scalar_String' => array('', 'ScalarOp'),
        'Stmt_Echo' => array(
            'PHPPHP\Engine\OpLines\EchoOp', 'UnaryOp',
            'exprs',
        ),
        'Stmt_Return' => array(
            'PHPPHP\Engine\OpLines\ReturnOp', 'UnaryOp',
            'expr',
        ),
    );

    public function compile(array $ast, Zval $returnContext = null) {
        $opArray = array();
        foreach ($ast as $node) {
            $opArray = array_merge($opArray, $this->compileNode($node, $returnContext));
        }
        return $opArray;
    }

    protected function compileNode(\PHPParser_Node $node, Zval $returnContext = null) {
        $nodeType = $node->getType();
        if (isset($this->operators[$nodeType])) {
            $class = $this->operators[$nodeType][0];
            $parseType = 'compile' . $this->operators[$nodeType][1];
            $param1 = isset($this->operators[$nodeType][2]) ? $this->operators[$nodeType][2] : null;
            $param2 = isset($this->operators[$nodeType][3]) ? $this->operators[$nodeType][3] : null;
            $ops = $this->$parseType($node, $returnContext, $class, $param1, $param2);
            return $ops;
        }

        $methodName = 'compile_' . $nodeType;
        if (!method_exists($this, $methodName)) {
            throw new \Exception($nodeType . ' not supported yet');
        }

        return call_user_func(array($this, 'compile_' . $nodeType), $node, $returnContext);
    }

    protected function compileChild(\PHPParser_Node $node, $childName, $returnContext = null) {
        $childNode = $node->$childName;
        if (is_null($childNode)) {
            return array();
        }
        if (!is_array($childNode)) {
            $childNode = array($childNode);
        }
        if ($returnContext && count($childNode) === 1 && is_scalar($childNode[0])) {
            $returnContext->value = $childNode[0];
            $returnContext->type = Zval::IS_STRING;
            return array();
        }
        return $this->compile($childNode, $returnContext);
    }

    protected function compileArrayOp($node, $returnContext = null, $handler, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        if ($returnContext) {
            $returnContext->zval->value[] = $op1;
        }
        return $ops;
    }

    protected function compileBinaryOp($node, $returnContext = null, $handler, $left = 'left', $right = 'right') {
        if (is_null($left)) $left = 'left';
        if (is_null($right)) $right = 'right';
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        $ops = array_merge($ops, $this->compileChild($node, $right, $op2));
        $opLine = new $handler($op1, $op2);
        if ($returnContext) {
            $opLine->result = $returnContext;
        } else {
            $opLine->result = Zval::ptrFactory();
        }
        $ops[] = $opLine;
        return $ops;
    }

    protected function compileUnaryOp($node, $returnContext = null, $handler, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        $opLine = new $handler($op1);
        if ($returnContext) {
            $opLine->result = $returnContext;
        } else {
            $opLine->result = Zval::ptrFactory();
        }
        $ops[] = $opLine;
        return $ops;
    }

    protected function compileScalarOp($node, $returnContext = null, $handler, $name = 'value', $sep = '') {
        if (is_null($name)) $name = 'value';
        if ($returnContext) {
            if ($sep) {
                $returnContext->value = implode($sep, $node->$name);
            } else {
                $returnContext->value = $node->$name;
            }
            $returnContext->rebuildType();
        }
        return array();
    }

    protected function compile_Param($node, $returnContext = null) {
        $defaultPtr = Zval::ptrFactory();
        $ops = $this->compileChild($node, 'default', $defaultPtr);
        if ($returnContext) {
            $returnContext->zval->value[] = array(
                'name' => $node->name,
                'default' => $defaultPtr,
                'ops' => $ops,
                'isRef' => $node->byRef,
                'type' => $node->type,
            );
        }
        return array();
    }

    protected function compile_Stmt_Function($node) {
        $stmts = $this->compileChild($node, 'stmts');
        $namePtr = Zval::ptrFactory();
        $ops = $this->compileChild($node, 'name', $namePtr);
        $paramsPtr = Zval::ptrFactory();
        $ops = array_merge($ops, $this->compileChild($node, 'params', $paramsPtr));

        $ops[] = new OpLines\FunctionDef(array(
            'name' => $namePtr,
            'stmts' => $stmts,
            'params' => $paramsPtr,
        ));

        return $ops;
    }

    protected function compile_Stmt_If($node) {
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, 'cond', $op1);

        // Jump targets: midOp is after the first if branch, endOp is after all branches
        $midOp = new OpLines\NoOp;
        $endOp = new OpLines\NoOp;

        $ops[] = new OpLines\JumpIfNot($op1, $midOp);

        $ops = array_merge($ops, $this->compileChild($node, 'stmts'));

        $ops[] = new OpLines\JumpTo($endOp);

        $ops[] = $midOp;

        $elseifs = $node->elseifs;
        foreach ($elseifs as $child) {
            $op1 = Zval::ptrFactory();
            $ops = array_merge($ops, $this->compileChild($child, 'cond', $op1));

            $midOp = new OpLines\NoOp;

            $ops[] = new OpLines\JumpIfNot($op1, $midOp);

            $ops = array_merge($ops, $this->compileChild($child, 'stmts'));

            $ops[] = new OpLines\JumpTo($endOp);

            $ops[] = $midOp;
        }

        $else = $node->else;
        if ($else) {
            $ops = array_merge($ops, $this->compileChild($node->else, 'stmts'));
        }

        $ops[] = $endOp;

        return $ops;
    }

    protected function compile_Stmt_While($node) {
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, 'cond', $op1);

        $endOp = new OpLines\NoOp;
        $ops[] = new OpLines\JumpIfNot($op1, $endOp);

        $whileOps = $this->compileChild($node, 'stmts');
        $ops = array_merge($ops, $whileOps);

        // jump back to cond
        $ops[] = new OpLines\JumpTo($ops[0]);

        $ops[] = $endOp;

        return $ops;
    }

    protected function compile_Stmt_Do($node) {
        $op1 = Zval::ptrFactory();

        $ops = $this->compileChild($node, 'stmts');
        $ops = array_merge($ops, $this->compileChild($node, 'cond', $op1));
        $ops[] = new OpLines\JumpIf($op1, $ops[0]);

        return $ops;
    }

    protected function compile_Stmt_InlineHtml($node) {
        return array(
            new OpLines\EchoOp(Zval::ptrFactory($node->value))
        );
    }
}