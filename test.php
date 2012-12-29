<?php

require_once __DIR__ . '/vendor/autoload.php';

$parser = new PHPParser_Parser(new PHPParser_Lexer);
$traverser = new PHPParser_NodeTraverser;
$traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver);

$ast = $parser->parse('<?php function foo($a = "bar") { echo $a; } foo();');
$ast = $traverser->traverse($ast);


$php = new PHPPHP\Engine\Parser;

$opArray = $php->parse($ast);

$executor = new PHPPHP\Engine\Executor;

$func = new PHPPHP\Engine\FunctionData($executor, PHPPHP\Engine\FunctionData::IS_INTERNAL);
$func->callback = 'strlen';
$executor->addFunction('strlen', $func);

$executor->execute($opArray);

