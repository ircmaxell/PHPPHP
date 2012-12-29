<?php

namespace PHPPHP\Engine;

interface OpCode {

    public function execute(\PHPPHP\Engine\ExecuteData $data);

}