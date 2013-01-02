<?php

namespace PHPPHP\Engine\OpLines;

class ContinueOp extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $num = 1;
        if ($this->op1) {
            $num = $this->op1->toLong();
        }
        $jump = null;
        for ($i = 0; $i < $num; $i++) {
            $jump = array_pop($data->statementStack);
            if ($jump && null === $jump->startOp) $i--;
        }
        if ($jump && null !== $jump->startOp) {
            $data->statementStack[] = $jump;
            $data->jump($jump->startOp);
        } else {
            throw new \RuntimeException('continue from without context');
        }
    }

}
