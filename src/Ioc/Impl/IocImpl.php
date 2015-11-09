<?php

namespace WebX\Ioc\Impl;

use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;

class IocImpl implements Ioc {

    private $pointersByInterface = array();
    private $instancesByInterface = array();
    private $configList = array();
    private $defsList = array();
    private $resolver;

    /**
     * @param \Closure|null $unknownResolver the function to be called by the IOC when a dependent construct parameter can't be resolved from registered implementation classes.
     * The function must be defined as
     * <code>
     *  function(\ReflectionParameter $constructorParameter, $id=null){};
     * </code>
     * If the closure return's a value!==NULL it will be used for the parameter otherwise the parameter's default value will be used,
     */
    public function __construct(\Closure $unknownResolver = null) {
        $this->resolver = $unknownResolver;
    }

    public function register($classNameOrObject, array $config = null) {
        try {
            $refClass = new \ReflectionClass($classNameOrObject);
            if ($refClass->isInstantiable()) {
                $pointer = array_push($this->defsList, $classNameOrObject)-1;
                $this->configList[] = $config;
                foreach ($refClass->getInterfaces() as $refInterface) {
                    $interface = $refInterface->getName();
                    if($id = isset($config["id"]) ? $config["id"] : null) {
                        if(!isset($this->pointersByInterface[$interface][$id])) {
                            $this->pointersByInterface[$interface][$id][] = $pointer;
                        } else {
                            throw new IocException(sprintf("Duplicate unique id '%s' registration for interface '%s' by class '%s'",$id,$interface,$refClass->getName()));
                        }
                    }
                    $this->pointersByInterface[$interface][null][] = $pointer;
                }
            } else {
                throw new IocException("Can't register an non-instantiable class. Hint: Register a concrete class name or an instance of a class.");
            }
        } catch(\ReflectionException $e) {
            throw new IocException(sprintf($e->getMessage(),$e));
        }
    }

    public function get($interfaceName,$id=null) {
        if(($instances = $this->resolveInstances($interfaceName,$id))) {
            return $instances[0];
        }
        throw new IocException(sprintf("Could not resolve any implementations for {$interfaceName}:[%s]", $id ?: "default"));
    }

    public function getAll($interfaceName) {
        return $this->resolveInstances($interfaceName) ?: [];
    }

    private function resolveInstances($interfaceName,$id = null) {
        if(NULL !== ($instances = isset($this->instancesByInterface[$interfaceName][$id]) ? $this->instancesByInterface[$interfaceName][$id] : null)) {
            return $instances;
        } else if (NULL !== ($pointers = isset($this->pointersByInterface[$interfaceName][$id]) ? $this->pointersByInterface[$interfaceName][$id] : null)) {
            $instances = [];
            foreach($pointers as $pointer) {
                if(is_string($def = &$this->defsList[$pointer])) {
                    $refClass = new \ReflectionClass($def);
                    $config = isset($this->configList[$pointer]) ? $this->configList[$pointer] : null;
                    if($constructor = $refClass->getConstructor()) {
                        if ($parameters = $constructor->getParameters()) {
                            $arguments = array();
                            foreach ($parameters as $p) {
                                $paramName = $p->getName();
                                if (($paramRefClass = $p->getClass()) && $paramRefClass->isInterface() && ($resolvedInstances = $this->resolveInstances($paramRefClass->getName(),isset($config["mappings"][$paramName]) ? $config["mappings"][$paramName] : null))) {
                                    $arguments[] = $resolvedInstances[0];
                                } else if (null !== ($value = isset($config["parameters"][$paramName]) ? $config["parameters"][$paramName] : null)) {
                                    $arguments[] = $value;
                                } else if ($this->resolver && (NULL !== ($instance = call_user_func_array($this->resolver,[$p,$config])))) {
                                    $arguments[] = $instance;
                                } else {
                                    if($p->isDefaultValueAvailable()) {
                                        $arguments[] = $p->getDefaultValue();
                                    } else {
                                        throw new IocException(sprintf("Unresolved parameter '%s' in '%s' without default value",$p->getName(),$refClass->getName()));
                                    }
                                }
                            }
                            $def = $refClass->newInstanceArgs($arguments);
                        } else {
                            $def = $refClass->newInstanceArgs();
                        }
                    } else {
                        $def = $refClass->newInstanceWithoutConstructor();
                    }
                }
                $instances[] = $def;
            }
            $this->instancesByInterface[$interfaceName][$id] = $instances;
            return $instances;
        }
    }
}