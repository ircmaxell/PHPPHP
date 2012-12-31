<?php

require_once __DIR__ . '/vendor/autoload.php';

$php = new PHPPHP\PHP;

list($options, $args) = parseCliArgs($argv);

if (isset($options['v'])) {
    echo "PHPPHP - Dev Master\n";
} elseif (isset($options['f'])) {
    $php->executeFile(realpath($options['f']));
} elseif (isset($options['r'])) {
    $php->execute('<?php ' . $options['r']);
} elseif (isset($args[0])) {
    $php->executeFile(realpath($args[0]));
} else {
    echo "Invalid arguments\n";
}

function parseCliArgs(array $args) {
    // first element is script name
    array_shift($args);

    $options = array();
    $arguments = array();

    $currentOption = null;
    foreach ($args as $arg) {
        if ($currentOption) {
            $options[$currentOption] = $arg;
            $currentOption = null;
        } elseif (strlen($arg) == 2 && $arg[0] == '-') {
            $currentOption = $arg[1];
        } else {
            $arguments[] = $arg;
        }
    }

    if ($currentOption) {
        $options[$currentOption] = '';
    }

    return array($options, $arguments);
}