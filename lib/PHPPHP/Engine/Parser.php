<?php

namespace PHPPHP\Engine;

class Parser {
    
    protected $operators = array(
        'Arg' => array(
            '',
            'ArrayOp',
            'value',
        ),
        'Expr_Assign' => array(
            'PHPPHP\Engine\OpCodes\Assign',
            'BinaryOp',
            'var',
            'expr',
        ),
        'Expr_AssignConcat' => array(
            'PHPPHP\Engine\OpCodes\AssignConcat',
            'BinaryOp',
            'var',
            'expr',
        ),
        'Expr_BooleanAnd' => array(
            'PHPPHP\Engine\OpCodes\BooleanAnd',
            'BinaryOp',
        ),
        'Expr_BooleanOr' => array(
            'PHPPHP\Engine\OpCodes\BooleanOr',
            'BinaryOp',
        ),
        'Expr_Concat' => array(
            'PHPPHP\Engine\OpCodes\Concat',
            'BinaryOp',
        ),
        'Expr_ConstFetch' => array(
            'PHPPHP\Engine\OpCodes\FetchConstant',
            'UnaryOp',
            'name',
        ),
        'Expr_FuncCall' => array(
            'PHPPHP\Engine\OpCodes\FunctionCall',
            'BinaryOp',
            'name',
            'args',
        ),
        'Expr_Isset' => array(
            'PHPPHP\Engine\OpCodes\IssetOp',
            'UnaryOp',
            'vars',
        ),
        'Expr_Mul' => array(
            'PHPPHP\Engine\OpCodes\Multiply',
            'BinaryOp',
        ),
        'Expr_Plus' => array(
            'PHPPHP\Engine\OpCodes\Add',
            'BinaryOp',
        ),
        'Expr_PostInc' => array(
            'PHPPHP\Engine\OpCodes\PostInc',
            'UnaryOp',
            'var',
        ),
        'Expr_Variable' => array(
            'PHPPHP\Engine\OpCodes\FetchVariable',
            'UnaryOp',
            'name',
        ),
        'Name' => array(
            '',
            'ScalarOp',
            'parts',
            '\\',
        ),
        'Scalar_DNumber' => array(
            '',
            'ScalarOp',
        ),
        'Scalar_LNumber' => array(
            '',
            'ScalarOp',
        ),
        'Scalar_String' => array(
            '',
            'ScalarOp',
        ),
        'Stmt_Echo' => array(
            'PHPPHP\Engine\OpCodes\EchoOp',
            'UnaryOp',
            'exprs',
        ),
        'Stmt_Return' => array(
            'PHPPHP\Engine\OpCodes\ReturnOp',
            'UnaryOp',
            'expr',
        ),
    );
    
    public function parse(array $ast, Zval $returnContext = null) {
        $opArray = array();
        foreach ($ast as $node) {
            $opArray = array_merge($opArray, $this->parseNode($node, $returnContext));
        }
        return $opArray;
    }

    protected function parseNode(\PHPParser_Node $node, Zval $returnContext = null) {
        $nodeType = $node->getType();
        if (isset($this->operators[$nodeType])) {
            $class = $this->operators[$nodeType][0];
            $parseType = 'parse' . $this->operators[$nodeType][1];
            $param1 = isset($this->operators[$nodeType][2]) ? $this->operators[$nodeType][2] : null;
            $param2 = isset($this->operators[$nodeType][3]) ? $this->operators[$nodeType][3] : null;
            list($opLine, $ops) = $this->$parseType($node, $returnContext, $param1, $param2);
            if ($opLine) {
                $opLine->handler = new $class;
            }
            return $ops;
        }
        return call_user_func(array($this, 'parse_' . $nodeType), $node, $returnContext);
    }

    protected function parseChild(\PHPParser_Node $node, $childName, $returnContext = null) {
        $childNode = $node->$childName;
        if (is_null($childNode)) {
            return array();
        }
        if (!is_array($childNode)) {
            $childNode = array($childNode);
        }
        if ($returnContext && count($childNode) === 1 && is_string($childNode[0])) {
            $returnContext->value = $childNode[0];
            $returnContext->type = Zval::IS_STRING;
            return array();
        }
        return $this->parse($childNode, $returnContext);
    }
    
    protected function parseArrayOp($node, $returnContext = null, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->parseChild($node, $left, $op1);
        if ($returnContext) {
            $returnContext->zval->value[] = $op1;
        }
        return array(false, $ops);
    }
    
    protected function parseBinaryOp($node, $returnContext = null, $left = 'left', $right = 'right') {
        if (is_null($left)) $left = 'left';
        if (is_null($right)) $right = 'right';
        $op1 = Zval::ptrFactory();
        $op2 = Zval::ptrFactory();
        $ops = $this->parseChild($node, $left, $op1);
        $ops = array_merge($ops, $this->parseChild($node, $right, $op2));
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

    protected function parseUnaryOp($node, $returnContext = null, $left = 'left') {
        if (is_null($left)) $left = 'left';
        $op1 = Zval::ptrFactory();
        $ops = $this->parseChild($node, $left, $op1);
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
    
    protected function parseScalarOp($node, $returnContext = null, $name = 'value', $sep = '') {
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

    protected function parse_Param($node, $returnContext = null) {
        $defaultPtr = Zval::ptrFactory();
        $ops = $this->parseChild($node, 'default', $defaultPtr);
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
    
    protected function parse_Stmt_Function($node) {
        $stmts = $this->parseChild($node, 'stmts');
        $namePtr = Zval::ptrFactory();
        $ops = $this->parseChild($node, 'name', $namePtr);
        $paramsPtr = Zval::ptrFactory();
        $ops = array_merge($ops, $this->parseChild($node, 'params', $paramsPtr));
        $opLine = new OpLine;
        $opLine->handler = new OpCodes\FunctionDef;
        $opLine->op1 = array(
            'name' => $namePtr,
            'stmts' => $stmts,
            'params' => $paramsPtr,
        );
        
        $ops[] = $opLine;
        return $ops;
    }
    
    protected function parse_Stmt_If($node) {
        $op1 = Zval::ptrFactory();
        $ops = $this->parseChild($node, 'cond', $op1);
        $opLine = new OpLine;
        $opLine->handler = new OpCodes\IfOp;
        $opLine->op1 = $op1;
        $ops[] = $opLine;

        $endOp = new OpLine;
        $endOp->handler = new OpCodes\NoOp;

        $ifOps = $this->parseChild($node, 'stmts');
        $ops = array_merge($ops, $ifOps);

        $jmpOp = new OpLine;
        $jmpOp->handler = new OpCodes\JumpTo;
        $jmpOp->op1 = $endOp;
        $ops[] = $jmpOp;
                
        $midOp = new OpLine;
        $midOp->handler = new OpCodes\NoOp;
        $opLine->op2 = $midOp;
        $ops[] = $midOp;
        
        $elseif = $node->elseifs;
        if ($elseif) {
            foreach ($elseif as $child) {
                $op1 = Zval::ptrFactory();
                $ops = array_merge($ops, $this->parseChild($child, 'cond', $op1));
                $opLine = new OpLine;
                $opLine->handler = new OpCodes\IfOp;
                $opLine->op1 = $op1;
                $ops[] = $opLine;
                $ifOps = $this->parseChild($child, 'stmts');
                $ops = array_merge($ops, $ifOps);
                $jmpOp = new OpLine;
                $jmpOp->handler = new OpCodes\JumpTo;
                $jmpOp->op1 = $endOp;
                $ops[] = $jmpOp;
                $midOp = new OpLine;
                $midOp->handler = new OpCodes\NoOp;
                $opLine->op2 = $midOp;
                $ops[] = $midOp;
            }
        }
        
        $else = $node->else;
        if ($else) {
            $elseOps = $this->parseChild($node->else, 'stmts');
            $ops = array_merge($ops, $elseOps);
        }
        
        $ops[] = $endOp;

        return $ops;
    }

    protected function parse_Stmt_InlineHtml($node) {
        $opLine = new OpLine;
        $opLine->handler = new OpCodes\EchoOp;
        $opLine->op1 = Zval::ptrFactory($node->value);
        return array(
            $opLine
        );
    }
}