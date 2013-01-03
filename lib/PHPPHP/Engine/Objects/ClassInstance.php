<?php

namespace PHPPHP\Engine\Objects;

use PHPPHP\Engine\ExecuteData;
use PHPPHP\Engine\Zval\Ptr;
use PHPPHP\Engine\Objects\ClassEntry;
use PHPPHP\Engine\Zval;

class ClassInstance
{
    private $ce;
    private $properties = array();

    public function __construct(ClassEntry $ce, array $properties) {
        $this->ce = $ce;
        $this->properties = $properties;

        array_map(function($property) {
            $property->addRef();
        }, $this->properties);
    }
    
    public function getClassEntry() {
        return $this->ce;
    }

    public function callConstructor(ExecuteData $data, array $args) {
        $parent = $this->ce;
        do {
            $ms = $parent->getMethodStore();
            if ($ms->exists('__construct')) {
                $this->callMethod($data, '__construct', $args);
            } else {
                $className = $parent->getName();
                if ($ms->exists($className)) {
                    $this->callMethod($data, $className, $args);
                    return;
                }
           }
        } while ($parent = $parent->getParent());
    }

    public function getProperty($name) {
        if (!isset($this->properties[$name])) {
            $value = Zval::ptrFactory();
            $this->properties[$name] = $value;
        } else {
            $value = $this->properties[$name];
        }
        return $value;
    }

    public function callMethod(ExecuteData $data, $name, array $args, Ptr $result = null) {
        $this->ce->callMethod($data, $this, $name, $args, $result);
    }

    public function __destruct() {
        array_map(function($property) {
            $property->delRef();
        }, $this->properties);
    }
}

