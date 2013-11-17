<?php

namespace PHPPHP\Engine;

// TODO: figure out how to delegate arguments to functionData
// currently it's hardcoded to array() which makes validation
// fail

$closureMethods = array(
	'__invoke' => new FunctionData\Internal(
		function(Executor $ex, array $params, Zval $return, Objects\ClassInstance $ci) {
			$fd = $ci->getProperty('functionData');
			if ($fd instanceof Zval) {
				$fd = $fd->getValue();
			}
			$fd->execute($ex, $params, $return);
		},
		false,
		array()
	),
);

$closureProperties = array(
	'functionData' => array(
		'default' => Zval::ptrFactory(null),
		'access' => Scope::ACC_PRIVATE | Scope::ACC_INTERNAL,
	),
);

return array(
	'Closure' => array(
		'methods' => $closureMethods,
		'properties' => $closureProperties,

	),
);