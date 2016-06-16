<?php

namespace WebX\Ioc\Impl;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;
use \ReflectionMethod;
use WebX\Ioc\IocNonResolvableException;

class IocImpl implements Ioc {

    private $pointersByInterface = array();
    private $instancesByInterface = array();
    private $configList = array();
    private $defsList = array();
    private $resolver;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    /**
     * @param Closure|null $unknownResolver the function to be called by the IOC when a dependent construct parameter can't be resolved from registered implementation classes.
     * The function must be defined as
     * <code>
     *  function(\IocNonResolvable $nonResolvable, Ioc $ioc){};
     * </code>
     * If the closure return's a value!==NULL it will be used for the parameter otherwise the parameter's default value will be used,
     */
    public function __construct(Closure $unknownResolver = null) {
        $this->resolver = $unknownResolver;
    }

    public function initStatic($className, $method, array $config = null) {
        if(!$this->proxyFactory) {
            $this->proxyFactory = new ProxyFactory($this);
        }
        try {
            $refClass = new ReflectionClass($className);
            $refMethod = $refClass->getMethod($method);
            if($refMethod->isStatic()) {
                if($arguments = $this->buildParameters($refMethod, $config, function(ReflectionParameter $p){
                    return $this->proxyFactory->createProxy($p->getClass());
                })) {
                    $refMethod->invokeArgs(null,$arguments);
                } else {
                    $refMethod->invoke(null);
                }
            } else {
                throw new IocException("The method {$className}::{$method} is not static");
            }
        } catch(\ReflectionException $e) {
            throw new IocException(sprintf($e->getMessage(),$e));
        }
    }

    public function register($classNameOrObject, array $config = null) {
        try {
            $refClass = new ReflectionClass($classNameOrObject);
            if ($refClass->isInstantiable()) {
                $pointer = array_push($this->defsList, $classNameOrObject)-1;
                $this->configList[] = $config;
                $id = isset($config["id"]) ? $config["id"] : null;
                foreach ($refClass->getInterfaces() as $refInterface) {
                    $interface = $refInterface->getName();
                    if($id) {
                        if(!isset($this->pointersByInterface[$interface][$id])) {
                            $this->pointersByInterface[$interface][$id][] = $pointer;
                        } else {
                            throw new IocException(sprintf("Duplicate unique id '%s' registration for interface '%s' by class '%s'",$id,$interface,$refClass->getName()));
                        }
                    }
                    $this->pointersByInterface[$interface][null][] = $pointer;
                }
                if(isset($config["registerClass"]) && $config["registerClass"]===true) {
                    $this->pointersByInterface[$refClass->getName()][null][] = $pointer;
                    if ($id) {
                        $this->pointersByInterface[$refClass->getName()][$id][] = $pointer;
                    }
                }
            } else {
                throw new IocException("Can't register an non-instantiable class. Hint: Register a concrete class name or an instance of a class.");
            }
        } catch(\ReflectionException $e) {
            throw new IocException(sprintf($e->getMessage(),$e));
        }
    }

    public function registerFactory(Closure $closure, $interfaces, array $config = null) {
        $pointer = array_push($this->defsList, $closure)-1;
        $this->configList[] = $config;
        if(!is_array($interfaces)) {
            $interfaces = [$interfaces];
        }
        foreach($interfaces as $interface) {
            if($id = isset($config["id"]) ? $config["id"] : null) {
                if(!isset($this->pointersByInterface[$interface][$id])) {
                    $this->pointersByInterface[$interface][$id][] = $pointer;
                } else {
                    throw new IocException(sprintf("Duplicate unique id '%s' registration for interface '%s' by factory'",$id,$interface));
                }
            }
            $this->pointersByInterface[$interface][null][] = $pointer;
        }
    }

    public function get($interfaceName, $id = null, $resolutionOrder = Ioc::RESOLUTION_ORDER_LAST) {
        if(($instances = $this->resolveInstances($interfaceName,$id))) {
            return $resolutionOrder===IOC::RESOLUTION_ORDER_LAST ? array_pop($instances) : array_shift($instances);
        }
        throw new IocNonResolvableException($interfaceName,$id);
    }

    public function getAll($interfaceName) {
        return $this->resolveInstances($interfaceName) ?: [];
    }

    private function resolveInstances($interfaceName,$id = null) {
        if(is_string($interfaceName)) {
            if (NULL !== ($instances = isset($this->instancesByInterface[$interfaceName][$id]) ? $this->instancesByInterface[$interfaceName][$id] : null)) {
                return $instances;
            } else if (NULL !== ($pointers = isset($this->pointersByInterface[$interfaceName][$id]) ? $this->pointersByInterface[$interfaceName][$id] : null)) {
                $instances = [];
                foreach ($pointers as $pointer) {
                    if (is_string($def = &$this->defsList[$pointer])) {
                        $config = isset($this->configList[$pointer]) ? $this->configList[$pointer] : null;
                        $def = $this->instantiate($def,$config);
                    } else if ($def instanceof Closure) {
                        $config = isset($this->configList[$pointer]) ? $this->configList[$pointer] : null;
                        $def = $this->invoke($def,$config);
                    }
                    $instances[] = $def;
                }
                $this->instancesByInterface[$interfaceName][$id] = $instances;
                return $instances;
            } else if ($this->resolver && (NULL !== ($resolution = call_user_func_array($this->resolver, [new IocNonResolvableImpl(null,new ReflectionClass($interfaceName), ["id"=>$id]),$this])))) {
                return [$resolution];
            }
        } else {
            throw new IocException("Interface name must be passed as a string");
        }
    }

    public function instantiate($className,array $config = null) {
        $refClass = new ReflectionClass($className);
        if ($constructor = $refClass->getConstructor()) {
            if ($arguments = $this->buildParameters($constructor,$config)) {
                return $refClass->newInstanceArgs($arguments);
            } else {
                return $refClass->newInstanceArgs();
            }
        } else {
            return $refClass->newInstanceWithoutConstructor();
        }
    }

    public function invoke(Closure $closure,array $config = null) {
        $refClosure = new ReflectionFunction($closure);
        if($arguments = $this->buildParameters($refClosure,$config)) {
            return call_user_func_array($closure, $arguments);
        } else {
            return $closure();
        }
    }

    private function buildParameters(ReflectionFunctionAbstract $reflectionMethod, array $config = null, Closure $resolver = null) {
        $arguments = array();
        foreach ($reflectionMethod->getParameters() as $p) {
            $paramName = $p->getName();
            if (null !== ($value = isset($config["parameters"][$paramName]) ? $config["parameters"][$paramName] : null)) {
                $arguments[] = $value;
            } else if (($paramRefClass = $p->getClass()) && ($resolvedInstances = $this->resolveInstances($paramRefClass->getName(), isset($config["mappings"][$paramName]) ? $config["mappings"][$paramName] : null))) {
                $arguments[] = $resolvedInstances[0];
            } else if ($p->isArray() && (null !== ($type = isset($config["types"][$paramName]) ? $config["types"][$paramName] : null))) {
                $arguments[] = $this->getAll($type);
            } else if ($this->resolver && (NULL !== ($resolution = call_user_func_array($this->resolver, [new IocNonResolvableImpl($p, ($reflectionMethod instanceof ReflectionMethod ? $reflectionMethod->getDeclaringClass() : null),$config),$this])))) {
                $arguments[] = $resolution;
            } else {
                if($resolver && ($value = $resolver($p))) {
                    $arguments[] = $value;
                } else if ($p->isDefaultValueAvailable()) {
                    $arguments[] = $p->getDefaultValue();
                } else {
                    throw new IocException(sprintf("Unresolved parameter '%s' in '%s' without default value", $p->getName(), ($reflectionMethod instanceof ReflectionMethod) ? ($reflectionMethod->getDeclaringClass() ? $reflectionMethod->getDeclaringClass()->getName() : "unknown") : "uknown"));
                }
            }
        }
        return $arguments;
    }
}