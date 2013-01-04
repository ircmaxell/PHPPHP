<?php

namespace PHPPHP\Engine\Objects;

use PHPPHP\Engine\ExecuteData;
use PHPPHP\Engine\Objects\ClassInstance;
use PHPPHP\Engine\FunctionData;
use PHPPHP\Engine\FunctionStore;
use PHPPHP\Engine\Zval\Ptr;
use PHPPHP\Engine\Zval;
use PHPPHP\Engine\ConstantStore;

class ClassEntry
{
    private $name;
    private $properties;
    private $methods;
    private $constants;
    private $parent;

    public function __construct($name, ClassEntry $parent = null)
    {
        $this->properties = array();
        $this->methods = new FunctionStore;
        $this->constants = new ConstantStore;
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

    public function declareProperty($name, Zval $defaultValue) {
        $this->properties[$name] = $defaultValue;
    }

    public function getMethodStore() {
        return $this->methods;
    }

    public function defineConstant($name, Zval $value) {
        $this->constants->register($name, $value->getZval());
    }

    public function getConstantStore() {
        return $this->constants;
    }

    public function getParent() {
        return $this->parent;
    }

    public function instantiate(ExecuteData $data, array $properties, array $args = array())
    {
        $parent = $this;
        do {
            $properties = array_merge($parent->properties, $properties);
        } while ($parent = $parent->parent);

        $instance = new ClassInstance($this, $properties);
        $instance->callConstructor($data, $args);
        return $instance;
    }

    public function findMethod($name) {
        $parent = $this;
        do {
            $exists = $parent->methods->exists($name);
        } while (!$exists && ($parent = $parent->parent));

        if (!$exists) {
            throw new \RuntimeException('Call To Undefined Function ' . $name);
        }

        return $parent->methods->get($name);
    }

    public function callMethod(ExecuteData $data, ClassInstance $ci = null, $name = '', array $args = array(), Ptr $result = null)
    {
        $method = $this->findMethod($name);
        if (!$result) {
            $result = Zval::ptrFactory();
        }
        $method->execute($data->executor, $args, $result, $ci);
    }
}

