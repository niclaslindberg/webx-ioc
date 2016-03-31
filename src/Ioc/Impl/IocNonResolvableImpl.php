<?php
/**
 * User: niclas
 * Date: 3/31/16
 * Time: 5:53 PM
 */

namespace WebX\Ioc\Impl;


use ReflectionClass;
use ReflectionParameter;
use WebX\Ioc\Ioc;
use WebX\Ioc\IocNonResolvable;

class IocNonResolvableImpl implements IocNonResolvable
{
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
    private $config;

    /**
     * IocUnresolvableImpl constructor.
     * @param ReflectionParameter $unresolvedParameter
     * @param ReflectionClass $unresolvedClass
     * @param Ioc $ioc
     * @param array $config
     */
    public function __construct(ReflectionParameter $unresolvedParameter = null, ReflectionClass $unresolvedClass=null,  array $config = null)
    {
        $this->unresolvedParameter = $unresolvedParameter;
        $this->unresolvedClass = $unresolvedClass;
        $this->config = $config;
    }


    public function unresolvedParameter()
    {
        return $this->unresolvedParameter;
    }

    public function unresolvedClass()
    {
        return $this->unresolvedClass;
    }

    public function config()
    {
        return $this->config;
    }

}