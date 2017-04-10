<?php

namespace WebX\Ioc;
use Closure;

/**
 * Inversion Of Control container
 * Interface Ioc
 * @package WebX\Ioc
 */
interface Ioc
{

    const RESOLUTION_ORDER_FIRST = 0;
    const RESOLUTION_ORDER_LAST = 1;

    /**
     * Registers a className or instance as implementation for all its interfaces with the container.
     * @param string|object $classNameOrObject
     * @param string|null $id if the instance should be bound to a unique id (unique per interface type).
     * @param array|null $config
     * <code>
     *  [
     *      "id" => "instanceId", (default null)
     *      "mappings" => [
     *          "constructorParamName1 => "instanceId1"
     *          "constructorParamNameN => "instanceIdN"
     *      ],
     *      "params" => [
     *          "constructorParamName1 => "someValue"
     *      ],
     *      "factory" => Closure (Closure to be invoked to instantiate the class)
     * ]
     * </code>.
     * @return void
     * @throws IocException if a registration error occurs (Reflection exception, Non-instantiable class or duplicated Id).
     */
    public function register($classNameOrObject, array $config = null);


    /**
     * Returns an object (first if multiple) that implements the interface.
     * @param string $interfaceName
     * @param string|null $id the name of the instance to resolve.
     * @param int $resolutionOrder return first or last instance when multiple occurances of an interface
     * @return object
     * @throws IocException if no instance that implements the interface can be found.
     */
    public function get($interfaceName, $id = null, $resolutionOrder = Ioc::RESOLUTION_ORDER_LAST);

    /**
     * Returns all objects that implement the interface.
     * @param $interfaceName
     * @return object[]
     */
    public function getAll($interfaceName);

    /**
     * Instantiates a given class and injects constructor dependencies
     * @param string $className the concreate class to be instantiated
     * @param array|null $config (Same as in register)
     * @return mixed
     */
    public function instantiate($className, array $config = null);

    /**
     * Invokes a Closure by injecting all its dependencies
     * @param Closure $closure the closure to be invoked
     * @param array|null $config (Same as in register)
     * @return mixed
     */
    public function invoke(Closure $closure,array $config = null);
}