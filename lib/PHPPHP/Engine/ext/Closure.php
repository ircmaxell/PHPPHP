<?php

namespace PHPPHP\Engine;

$closureMethods = array(
	'__invoke' => new FunctionData\Internal(
		function(Executor $ex, array $params, Zval $return, Objects\ClassInstance $ci) {
			$fd = $ci->getProperty('functionData');
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