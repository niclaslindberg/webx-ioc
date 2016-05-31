<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocDependentArrayTest extends \PHPUnit_Framework_TestCase
{

    public function testNoTypeFailsA() {
        $ioc = new IocImpl();

        $ioc->register(A::class);
        $ioc->register(DependentArrayA::class);
        try {
            $dependentA = $ioc->get(IDependentArrayA::class);
            $this->fail("Should never reach here");
        }
        catch(IocException $e) {}
    }

    public function testWithTypeSuccessA() {
        $ioc = new IocImpl();

        $ioc->register(B::class);
        $ioc->register(A::class);
        $ioc->register(A2::class);
        $ioc->register(DependentArrayA::class,["types"=>["a"=>IA::class]]);

        $dependentA = $ioc->get(IDependentArrayA::class);

        $this->assertEquals(2,count($dependentA->getAs()));
        $this->assertInstanceOf(IA::class,$dependentA->getAs()[0]);
        $this->assertInstanceOf(IA::class,$dependentA->getAs()[1]);
    }

}