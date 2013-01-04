<?php

namespace PHPPHP\Ext\Strings;

use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\Executor;
use PHPPHP\Engine\Zval;
use PHPPHP\Engine\ParamData;

$implode = new FunctionData\Internal(
                function(Executor $executor, array $args, Zval $return) {
                    $array = $args[0];
                    $glue = '';
                    if ($args[1]) {
                        if ($args[0]->isArray()) {
                            $glue = $args[1]->toString();
                        } else {
                            $glue = $args[0]->toString();
                            $array = $args[1];
                        }
                    }
                    if ($array->isArray()) {
                        $result = '';
                        $sep = '';
                        foreach ($array->getArray() as $value) {
                            $result .= $sep . $value->toString();
                            $sep = $glue;
                        }
                        $return->setValue($result);
                    } else {
                        var_dump($args);
                        throw new \Exception('Something failed! ' . $executor->getCurrent()->opArray->getFileName());
                    }
                },
                false,
                array(
                    new ParamData('glue'),
                    new ParamData('pieces', false, '', true),
                )
);

return array(
    'implode' => $implode,
    'join'    => new FunctionData\Alias($implode),
);
