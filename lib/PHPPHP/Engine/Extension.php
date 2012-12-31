<?php

namespace PHPPHP\Engine;

interface Extension {

    public function register(\PHPPHP\Engine\Executor $executor);

    public function getName();
}