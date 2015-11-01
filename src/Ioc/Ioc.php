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