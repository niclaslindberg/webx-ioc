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
     * @return void
     */
    public function register($classNameOrObject);

    /**
     * Returns an object (first if multiple) that implements the interface.
     * @param $interfaceName
     * @return object
     * @throws IocException if no instance that implements the interface can be found.
     */
    public function get($interfaceName);

    /**
     * Returns all objects that implement the interface.
     * @param $interfaceName
     * @return object[]
     */
    public function getAll($interfaceName);

}