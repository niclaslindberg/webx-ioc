<?php
namespace WebX\Ioc\Impl;

use ReflectionClass;
use ReflectionParameter;
use WebX\Ioc\Ioc;
use WebX\Ioc\IocNonResolvable;

class IocNonResolvableImpl implements IocNonResolvable {
    /**
     * @var ReflectionParameter
     */
    private $unresolvedParameter;

    /**
     * @var ReflectionClass
     */
    private $unresolvedClass;

    /**
     * @var array|null
     */
    private $parameters;

    /**
     * IocUnresolvableImpl constructor.
     * @param ReflectionParameter $unresolvedParameter
     * @param ReflectionClass $unresolvedClass
     * @param Ioc $ioc
     * @param array $parameters
     */
    public function __construct(ReflectionParameter $unresolvedParameter = null, ReflectionClass $unresolvedClass=null, array $parameters = null, $id=null) {
        $this->unresolvedParameter = $unresolvedParameter;
        $this->unresolvedClass = $unresolvedClass;
        $this->parameters = $parameters;
        $this->id = $id;
    }


    public function unresolvedParameter() {
        return $this->unresolvedParameter;
    }

    public function unresolvedClass() {
        return $this->unresolvedClass;
    }

    public function parameters() {
        return $this->parameters;
    }

    public function id() {
        return $this->id;
    }


}