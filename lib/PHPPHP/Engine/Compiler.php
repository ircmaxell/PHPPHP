<?php

namespace PHPPHP\Engine;

class Compiler {

    protected $operators = array(
        'Arg' => array(
            '', 'ArrayOp',
            'value',
        ),
        'Expr_Assign' => array(
            'PHPPHP\Engine\OpCodes\Assign', 'BinaryOp',
            'var', 'expr',
        ),
        'Expr_AssignConcat' => array(
            'PHPPHP\Engine\OpCodes\AssignConcat', 'BinaryOp',
            'var', 'expr',
        ),
        'Expr_BooleanAnd'     => array('PHPPHP\Engine\OpCodes\BooleanAnd', 'BinaryOp'),
        'Expr_BooleanOr'      => array('PHPPHP\Engine\OpCodes\BooleanOr', 'BinaryOp'),
        'Expr_Concat'         => array('PHPPHP\Engine\OpCodes\Concat', 'BinaryOp'),
        'Expr_Smaller'        => array('PHPPHP\Engine\OpCodes\Smaller', 'BinaryOp'),
        'Expr_SmallerOrEqual' => array('PHPPHP\Engine\OpCodes\SmallerOrEqual', 'BinaryOp'),
        'Expr_Greater' => array(
            'PHPPHP\Engine\OpCodes\Smaller', 'BinaryOp',
            'right', 'left'
        ),
        'Expr_GreaterOrEqual' => array(
            'PHPPHP\Engine\OpCodes\SmallerOrEqual', 'BinaryOp',
            'right', 'left',
        ),
        'Expr_Equal'        => array('PHPPHP\Engine\OpCodes\Equal', 'BinaryOp'),
        'Expr_NotEqual'     => array('PHPPHP\Engine\OpCodes\NotEqual', 'BinaryOp'),
        'Expr_Identical'    => array('PHPPHP\Engine\OpCodes\Identical', 'BinaryOp'),
        'Expr_NotIdentical' => array('PHPPHP\Engine\OpCodes\NotIdentical', 'BinaryOp'),
        'Expr_ConstFetch' => array(
            'PHPPHP\Engine\OpCodes\FetchConstant', 'UnaryOp',
            'name',
        ),
        'Expr_FuncCall' => array(
            'PHPPHP\Engine\OpCodes\FunctionCall', 'BinaryOp',
            'name', 'args'
        ),
        'Expr_Include' => array(
            'PHPPHP\Engine\OpCodes\IncludeOp', 'BinaryOp',
            'type', 'expr'
        ),
        'Expr_Isset' => array(
            'PHPPHP\Engine\OpCodes\IssetOp', 'UnaryOp',
            'vars',
        ),
        'Expr_Mul' => array('PHPPHP\Engine\OpCodes\Multiply', 'BinaryOp'),
        'Expr_Plus' => array('PHPPHP\Engine\OpCodes\Add', 'BinaryOp'),
        'Expr_PostInc' => array(
            'PHPPHP\Engine\OpCodes\PostInc', 'UnaryOp',
            'var',
        ),
        'Expr_Variable' => array(
            'PHPPHP\Engine\OpCodes\FetchVariable', 'UnaryOp',
            'name',
        ),
        'Expr_FetchConstant' => array(
            'PHPPHP\Engine\OpCodes\FetchConstant', 'UnaryOp',
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
            'PHPPHP\Engine\OpCodes\EchoOp', 'UnaryOp',
            'exprs',
        ),
        'Stmt_Return' => array(
            'PHPPHP\Engine\OpCodes\ReturnOp', 'UnaryOp',
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
            list($opLine, $ops) = $this->$parseType($node, $returnContext, $param1, $param2);
            if ($opLine) {
                $opLine->handler = new $class;
            }
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

    protected function compileArrayOp($node, $returnContext = null, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        if ($returnContext) {
            $returnContext->zval->value[] = $op1;
        }
        return array(false, $ops);
    }

    protected function compileBinaryOp($node, $returnContext = null, $left = 'left', $right = 'right') {
        if (is_null($left)) $left = 'left';
        if (is_null($right)) $right = 'right';
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        $ops = array_merge($ops, $this->compileChild($node, $right, $op2));
        $opLine = new OpLine;
        $opLine->op1 = $op1;
        $opLine->op2 = $op2;
        if ($returnContext) {
            $opLine->result = $returnContext;
        } else {
            $opLine->result = Zval::ptrFactory();
        }
        $ops[] = $opLine;
        return array($opLine, $ops);
    }

    protected function compileUnaryOp($node, $returnContext = null, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->compileChild($node, $left, $op1);
        $opLine = new OpLine;
        $opLine->op1 = $op1;
        if ($returnContext) {
            $opLine->result = $returnContext;
        } else {
            $opLine->result = Zval::ptrFactory();
        }
        $ops[] = $opLine;
        return array($opLine, $ops);
    }

    protected function compileScalarOp($node, $returnContext = null, $name = 'value', $sep = '') {
        if (is_null($name)) $name = 'value';
        if ($returnContext) {
            if ($sep) {
                $returnContext->value = implode($sep, $node->$name);
            } else {
                $returnContext->value = $node->$name;
            }
            $returnContext->rebuildType();
        }
        return array(false, array());
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

        $ops[] = new OpLine(new OpCodes\FunctionDef, array(
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
        $midOp = new OpLine(new OpCodes\NoOp);
        $endOp = new OpLine(new OpCodes\NoOp);

        $ops[] = new OpLine(new OpCodes\JumpIfNot, $op1, $midOp);

        $ops = array_merge($ops, $this->compileChild($node, 'stmts'));

        $ops[] = new OpLine(new OpCodes\JumpTo, $endOp);

        $ops[] = $midOp;

        $elseifs = $node->elseifs;
        foreach ($elseifs as $child) {
            $op1 = Zval::ptrFactory();
            $ops = array_merge($ops, $this->compileChild($child, 'cond', $op1));

            $midOp = new OpLine(new OpCodes\NoOp);

            $ops[] = new OpLine(new OpCodes\JumpIfNot, $op1, $midOp);

            $ops = array_merge($ops, $this->compileChild($child, 'stmts'));

            $ops[] = new OpLine(new OpCodes\JumpTo, $endOp);

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

        $endOp = new OpLine(new OpCodes\NoOp);
        $ops[] = new OpLine(new OpCodes\JumpIfNot, $op1, $endOp);

        $whileOps = $this->compileChild($node, 'stmts');
        $ops = array_merge($ops, $whileOps);

        // jump back to cond
        $ops[] = new OpLine(new OpCodes\JumpTo, $ops[0]);

        $ops[] = $endOp;

        return $ops;
    }

    protected function compile_Stmt_Do($node) {
        $op1 = Zval::ptrFactory();

        $ops = $this->compileChild($node, 'stmts');
        $ops = array_merge($ops, $this->compileChild($node, 'cond', $op1));
        $ops[] = new OpLine(new OpCodes\JumpIf, $op1, $ops[0]);

        return $ops;
    }

    protected function compile_Stmt_InlineHtml($node) {
        $opLine = new OpLine;
        $opLine->handler = new OpCodes\EchoOp;
        $opLine->op1 = Zval::ptrFactory($node->value);
        return array(
            $opLine
        );
    }
}