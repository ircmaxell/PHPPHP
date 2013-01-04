<?php

namespace PHPPHP\Engine\OpLines;

use PHPPHP\Engine\Zval;

class AssignDiv extends BinaryAssign {

    public function execute(\PHPPHP\Engine\ExecuteData $data) {
        $value = $this->getValue();
        if (0 == $value) {
            $this->setValue(false);
        } else {
            $this->setValue($value / $this->op2->getValue());
        }

        $data->nextOp();
    }

}
