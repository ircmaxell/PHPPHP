<?php

namespace PHPPHP\Engine\Objects;

use PHPPHP\Engine\ExecuteData;
use PHPPHP\Engine\Objects\ClassInstance;
use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\FunctionStore;
use PHPPHP\Engine\Zval\Ptr;
use PHPPHP\Engine\Zval;

class ClassEntry
{
    private $name;
    private $methods;
    private $parent;

    public function __construct($name, ClassEntry $parent = null)
    {
        $this->methods = new FunctionStore;
        $this->name = $name;
        $this->parent = $parent;
    }

    public function isInstanceOf($name) {
        $parent = $this;
        $name = strtolower($name);
        do {
            if ($name == strtolower($parent->name)) {
                return true;
            }
        } while ($parent = $parent->parent);
        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMethodStore() {
        return $this->methods;
    }

    public function getParent() {
        return $this->parent;
    }

    public function instantiate(ExecuteData $data, array $properties, array $args = array())
    {
        $instance = new ClassInstance($this, $properties);
        $instance->callConstructor($data, $args);
        return $instance;
    }

    public function callMethod(ExecuteData $data, ClassInstance $ci, $name, array $args, Ptr $result = null)
    {
        $parent = $this;
        do {
            $exists = $parent->methods->exists($name);
        } while (!$exists && ($parent = $parent->parent));

        if (!$exists) {
            throw new \RuntimeException('Call To Undefined Function ' . $name);
        }

        $method = $parent->methods->get($name);
        if (!$result) {
            $result = Zval::ptrFactory();
        }
        $method->execute($data->executor, $args, $result, $ci);
    }
}

