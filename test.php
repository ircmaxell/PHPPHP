<?php

require_once __DIR__ . '/vendor/autoload.php';

$code = '<?php var_dump("foobar");';

$php = new PHPPHP\PHP;
$php->execute($code);

