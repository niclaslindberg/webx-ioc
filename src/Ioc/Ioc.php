<?php

namespace WebX\Ioc;
/**
 * Inversion Of Control container
 * Interface Ioc
 * @package WebX\Ioc
 */
interface Ioc {

    /**
     * Registers an a classname or an instance with all its implemented interfaces with the container.
     * @param $classNameOrObject
     * @return mixed
     */
    public function register($classNameOrObject);

    /**
     * @param \Closure $closure the function to be called by the IOC when a dependent construct parameter can't be resolved from registered implementation classes.
     * The function must be defined by
     * <code>
     *  function(\ReflectionParameter $constructorParameterName, \ReflectionClass $contructorParameterType);
     * </code>
     * If the closure return's a value it will be used the constructor parameter value if not the default,
     * @return void
     */
    public function setUnknownResolver(\Closure $closure);

    /**
     * Returns an object that implements the interface.
     * @param $interfaceName
     * @return mixed
     */
    public function create($interfaceName);

    /**
     * Returns all objects that implements the interface.
     * @param $interfaceName
     * @return mixed
     */
    public function createAll($interfaceName);

}