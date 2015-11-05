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
     * @return void
     * @throws IocException if a registration error occurs (Reflection exception, Non-instantiable class or duplicated Id).
     */
    public function register($classNameOrObject, $id = null);

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