<?php

namespace PHPPHP\Engine\Objects;

use PHPPHP\Engine\ExecuteData;
use PHPPHP\Engine\Objects\ClassInstance;
use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\Zval\Ptr;
use PHPPHP\Engine\Zval;

class ClassEntry
{
    private $name;
    private $methods = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function instantiate(ExecuteData $data, array $properties, array $args = array())
    {
        return new ClassInstance($this, $properties);
    }

    public function addMethod($name, FunctionData $method)
    {
        $this->methods[$name] = $method;
    }

    public function callMethod(ExecuteData $data, ClassInstance $ci, $name, array $args, Ptr $result = null)
    {
        if (!isset($this->methods[$name])) {
            throw new \RuntimeException(sprintf('Call to undefined method %s::%s()', $this->getName(), $name));
        }
        $method = $this->methods[$name];
        if (!$result) {
            $result = Zval::ptrFactory();
        }
        $method->execute($data->executor, $args, $result);
    }
}

