<?php

namespace PHPPHP\Engine\OpLines;

use PHPParser_Node_Expr_Include as IncludeNode;

class IncludeOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $fileName = $this->op2->toString();
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = $data->executor->executorGlobals->cwd . '/' . $fileName;
        }
        $fileName = realpath($fileName);
        if (!is_file($fileName)) {
            throw new \RuntimeException('Including bad file!');
        }
        switch ($this->op1->getValue()) {
            case IncludeNode::TYPE_INCLUDE_ONCE:
            case IncludeNode::TYPE_REQUIRE_ONCE:
                if ($data->executor->hasFile($fileName)) {
                    break;
                }
            case IncludeNode::TYPE_INCLUDE:
            case IncludeNode::TYPE_REQUIRE:
                $opCodes = $data->executor->compileFile($fileName);
                $data->executor->execute($opCodes);
        }

        $data->nextOp();
    }

}