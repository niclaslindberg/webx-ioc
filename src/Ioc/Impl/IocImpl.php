<?php

namespace WebX\Ioc\Impl;

use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;

class IocImpl implements Ioc {

    protected $pointersByInterface = array();

    protected $instancesByInterface = array();

    private $defsList = array();

    /**
     * @var \Closure
     */
    protected $resolver;

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

    public function setUnknownResolver(\Closure $closure) {
        $this->resolver = $closure;
    }

    public function create($interfaceName) {
        return $this->resolveInstances($interfaceName)[0];
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
            return $this->instancesByInterface[$interfaceName] = $instances;
        } else {
            throw new IocException("Could not resolve {$interfaceName}");
        }
    }

    private function instantiate(\ReflectionClass $refClass) {

        if($constructor = $refClass->getConstructor()) {
            if ($parameters = $constructor->getParameters()) {
                $arguments = array();
                foreach ($parameters as $p) {
                    $paramRefClass = $p->getClass();
                    if ($paramRefClass->isInterface() && ($instance = $this->instantiate($paramRefClass))) {
                        $arguments[] = $instance;
                    } else {
                        if (NULL !== ($instance = $this->resolver($p, $refClass))) {
                            $arguments[] = $instance;
                        } else {
                            $arguments[] = $p->isDefaultValueAvailable() ? $p->defaultValue() : null;
                        }
                    }
                }
            } else {
                return $refClass->newInstanceArgs();
            }
        } else {
            return $refClass->newInstanceWithoutConstructor();
        }
    }
}