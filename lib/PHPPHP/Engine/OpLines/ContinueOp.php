<?php

namespace PHPPHP\Engine\OpLines;

class ContinueOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $num = 1;
        if ($this->op1) {
            $num = $this->op1->toLong();
        }
        $jump = null;
        $stack = $data->statementStack;
        for ($i = 0; $i < $num; $i++) {
            $jump = array_pop($data->statementStack);
            if ($jump && !$jump->startOp) $i--;
        }
        if ($jump && $jump->startOp) {
            if (is_int($jump->startOp)) {
                $data->jump($jump->startOp);
            } elseif ($jump->startOp instanceof \PHPPHP\Engine\OpLine) {
                $data->jumpTo($jump->startOp);
            }
        }
        throw new \RuntimeException('break from without context');

    }

}