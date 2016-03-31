<?php

namespace WebX\Ioc;


use ReflectionClass;
use ReflectionParameter;

interface IocNonResolvable
{

    /**
     * The parameter that Ioc is trying to resolve. <code>null</code> if not bound to a parameter
     * @return ReflectionParameter|null
     */
    public function unresolvedParameter();

    /**
     * The reflection class of the object to be resolved
     * @return ReflectionClass|null
     */
    public function unresolvedClass();

    /**
     * .The optional id of the instance to be resolved
     * @return array|null
     */
    public function config();

}