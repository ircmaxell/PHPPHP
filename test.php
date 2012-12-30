<?php

require_once __DIR__ . '/vendor/autoload.php';

$code = '<?php echo strrev("bar");';

$php = new PHPPHP\PHP;
$php->execute($code);

