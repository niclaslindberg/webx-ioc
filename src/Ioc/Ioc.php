<?php

namespace WebX\Ioc;
/**
 * Inversion Of Control container
 * Interface Ioc
 * @package WebX\Ioc
 */
interface Ioc {

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
     *      ]
     * ]
     * </code>.
     * @return void
     * @throws IocException if a registration error occurs (Reflection exception, Non-instantiable class or duplicated Id).
     */
    public function register($classNameOrObject, array $config = null);

    /**
     * @param $className the className of the class to be statically initialized
     * @param $method the name of the static method to be called
     * @return void
     */
    public function initStatic($className,$method);


    /**
     * Returns an object (first if multiple) that implements the interface.
     * @param string $interfaceName
     * @param string|null $id the name of the instance to resolve.
     * @return object
     * @throws IocException if no instance that implements the interface can be found.
     */
    public function get($interfaceName, $id = null);

    /**
     * Returns all objects that implement the interface.
     * @param $interfaceName
     * @return object[]
     */
    public function getAll($interfaceName);

}