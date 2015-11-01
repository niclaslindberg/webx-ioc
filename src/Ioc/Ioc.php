<?php

namespace WebX\Ioc;
/**
 * Inversion Of Control container
 * Interface Ioc
 * @package WebX\Ioc
 */
interface Ioc {

    /**
     * Registers this classname or instance with as implementation for all its interfaces with the container.
     * @param string|object $classNameOrObject
     * @return void
     */
    public function register($classNameOrObject);

    /**
     * Returns an instance that implements the interface.
     * @param $interfaceName
     * @return object
     */
    public function get($interfaceName);

    /**
     * Returns all objects that implement the interface.
     * @param $interfaceName
     * @return mixed
     */
    public function getAll($interfaceName);

}