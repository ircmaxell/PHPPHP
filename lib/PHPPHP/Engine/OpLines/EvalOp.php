<?php

namespace PHPPHP\Engine\OpLines;

class EvalOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $code = $this->op1->toString();
        $opCodes = $data->executor->compile('<?php ' . $code);
        $return = $data->executor->execute($opCodes, $data->symbolTable);
        if ($return) {
            $this->result->value = $return->value;
            $this->result->rebuildType();
        }
        $data->nextOp();
    }

}