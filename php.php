<?php

require_once __DIR__ . '/vendor/autoload.php';

$php = new PHPPHP\PHP;

if (isset($argv[1]) && !isset($argv[2])) {
    $file = realpath($argv[1]);
    $php->executeFile($file);
} elseif (isset($argv[1]) && $argv[1] == '-r') {
    $code = $argv[2];
    $php->execute('<?php ' . $code);
}
