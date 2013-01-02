<?php

namespace PHPPHP\Engine\OpLines;

class BreakOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $num = 1;
        if ($this->op1) {
            $num = $this->op1->toLong();
        }
        $jump = null;
        for ($i = 0; $i < $num; $i++) {
            $jump = array_pop($data->statementStack);
        }
        if ($jump) {
            $data->jumpTo($jump);
            $data->nextOp();
        } else {
            throw new \RuntimeException('break from without context');
        }

    }

}