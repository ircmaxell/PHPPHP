<?php

$funcs = get_defined_functions();

$output = "<?php
use PHPPHP\Engine\ParamData;

return array(\n";
foreach ($funcs['internal'] as $func) {
    $output .= "    array('$func', ";
    $r = new ReflectionFunction($func);
    $output .= $r->returnsReference() ? 'true' : 'false';
    $output .= ', array(';
    foreach ($r->getParameters() as $param) {
        $output .= "new ParamData('" . $param->getName() . "', ";
        $output .= $param->isPassedByReference() ? "true, " : "false, ";
        $output .= ", '', " . $param->isOptional() ? "true" : 'false';
        $output .= "),";
    }
    $output .= ")),\n";
}
$output .= ");";
file_put_contents(__DIR__ . '/aliases.php', $output);