<?php

namespace PHPPHP\Engine\Output;

class Std extends \PHPPHP\Engine\Output {

    public function write($data) {
        echo $data;
    }

}