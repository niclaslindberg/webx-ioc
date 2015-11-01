<?php

namespace WebX\Ioc\Impl;

use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;

class IocImpl implements Ioc {

    private $pointersByInterface = array();

    private $instancesByInterface = array();

    private $defsList = array();

    private $resolver;

    /**
     * @param \Closure|null $unknownResolver the function to be called by the IOC when a dependent construct parameter can't be resolved from registered implementation classes.
     * The function must be defined as
     * <code>
     *  function(\ReflectionParameter $constructorParameterName, \ReflectionClass $contructorParameterType);
     * </code>
     * If the closure return's a value it will be used the constructor parameter value if not the default,
     */
    public function __construct(\Closure $unknownResolver = null) {
        $this->resolver = $unknownResolver ?: function(\ReflectionClass $refClass, \ReflectionParameter $refParam) {
            throw new IocException(sprintf("Non resolved unknown: %s.%s",[$refClass->getName(),$refParam->getName()]));
        };
    }

    public function register($classNameOrObject) {
        $refClass = new \ReflectionClass($classNameOrObject);
        if($refClass->isInstantiable()) {
            $this->defsList[] = $classNameOrObject;
            $pointer = count($this->defsList)-1;
            foreach ($refClass->getInterfaces() as $refInterface) {
                $this->pointersByInterface[$refInterface->getName()][] = $pointer;
            }
        } else {
            throw new IocException("Can't register an non-instantiable type. Hint: Register a concrete class name or an instance of a class.");
        }
    }

    public function create($interfaceName) {
        if($instances = $this->resolveInstances($interfaceName)) {
            return $instances[0];
        }
        throw new IocException("Could not resolve any implementations for {$interfaceName}");
    }

    public function createAll($interfaceName) {
        return $this->resolveInstances($interfaceName);
    }

    private function resolveInstances($interfaceName) {
        if(NULL !== ($instances = isset($this->instancesByInterface[$interfaceName]) ? $this->instancesByInterface[$interfaceName] : null)) {
            return $instances;
        } else if (NULL !== ($pointers = isset($this->pointersByInterface[$interfaceName]) ? $this->pointersByInterface[$interfaceName] : null)) {
            $instances = [];
            foreach($pointers as $pointer) {
                $def = &$this->defsList[$pointer];
                if(is_string($def)) {
                    $def = $this->instantiate(new \ReflectionClass($def));
                }
                $instances[] = $def;
            }
            $this->instancesByInterface[$interfaceName] = $instances;
            return $instances;
        } else {
           return [];
        }
    }

    private function instantiate(\ReflectionClass $refClass) {
        if($constructor = $refClass->getConstructor()) {
            if ($parameters = $constructor->getParameters()) {
                $arguments = array();
                foreach ($parameters as $p) {
                    $paramRefClass = $p->getClass();
                    if ($paramRefClass->isInterface() && ($instances = $this->resolveInstances($paramRefClass->getName()))) {
                        $arguments[] = $instances[0];
                    } else {
                        if (NULL !== ($instance = $this->resolver($p, $refClass))) {
                            $arguments[] = $instance;
                        } else {
                            $arguments[] = $p->isDefaultValueAvailable() ? $p->defaultValue() : null;
                        }
                    }
                    return $refClass->newInstanceArgs($arguments);
                }
            } else {
                return $refClass->newInstanceArgs();
            }
        } else {
            return $refClass->newInstanceWithoutConstructor();
        }
    }
}