<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocDependentArrayTest extends \PHPUnit_Framework_TestCase
{

    public function testWithTypeSuccessA() {
        $ioc = new IocImpl();

        $ioc->register(B::class);
        $ioc->register(A::class);
        $ioc->register(A2::class);
        $ioc->register(DependentArrayA::class,["parameters"=>["a"=>IA::class]]);

        $dependentA = $ioc->get(IDependentArrayA::class);

        $this->assertEquals(2,count($dependentA->getAs()));
        $this->assertInstanceOf(IA::class,$dependentA->getAs()[0]);
        $this->assertInstanceOf(IA::class,$dependentA->getAs()[1]);
    }

}