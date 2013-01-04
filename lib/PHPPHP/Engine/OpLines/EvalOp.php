<?php

namespace PHPPHP\Engine\OpLines;

class EvalOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $fileName = $data->opArray->getFileName() . '(' . $this->lineno . ") : eval()'d code";
        $code = $this->op1->toString();
        $opCodes = $data->executor->compile('<?php ' . $code, $fileName);
        $return = $data->executor->execute($opCodes, $data->symbolTable);
        if ($return) {
            $this->result->setValue($return);
        }
        $data->nextOp();
    }

}