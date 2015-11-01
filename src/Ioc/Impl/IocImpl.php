<?php

namespace WebX\Ioc\Impl;

use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;

class IocImpl implements Ioc {

    protected $defByType = array();

    protected $instanceByType = array();

    /**
     * @var \Closure
     */
    protected $resolver;

    public function __construct(\Closure $unknownResolver = null) {
        $this->resolver = $unknownResolver ?: function(\ReflectionClass $refClass, \ReflectionParameter $refParam) {
            throw new IocException(sprintf("Non resolved unknown: %s.%s",$refClass->getShortName(),$refParam->getName()));
        };
    }

    public function register($classNameOrObject) {
        $refClass = new \ReflectionClass($classNameOrObject);
        if($refClass->isInstantiable()) {
            foreach ($refClass->getInterfaces() as $refInterface) {
                $this->defByType[$refInterface->getName()][] = $classNameOrObject;
            }
        } else {
            throw new IocException("Not an instantiable class.");
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
        if(NULL !== ($instances = isset($this->instanceByType[$interfaceName]) ? $this->instanceByType[$interfaceName] : null)) {
            return $instances;
        } else if (NULL !== ($defs = isset($this->defByType[$interfaceName]) ? $this->defByType[$interfaceName] : null)) {
            $instances = [];
            foreach($defs as $def) {
                if(is_string($def)) {
                    $instances[] = $this->instantiate(new \ReflectionClass($def));
                } else {
                    $instances[] = $def;
                }
                $this->instanceByType[$interfaceName] = $instances;
            }
            return $instances;
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