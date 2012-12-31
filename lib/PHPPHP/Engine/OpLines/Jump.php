<?php

namespace PHPPHP\Engine\OpLines;

class Jump extends \PHPPHP\Engine\OpLine {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $data->jump($this->op1->value);
    }

}