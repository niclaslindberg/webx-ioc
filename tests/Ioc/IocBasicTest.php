<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocBasicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterInterfaceFails() {
        $ioc = new IocImpl();
        $ioc->register(IA::class);
    }

    public function registerInstanceAndResolvePass() {
        $ioc = new IocImpl();
        $a = new A();
        $ioc->register($a);
        $a2 = $ioc->get(IA::class);
        $this->assertSame($a,$a2);
    }

    public function registerItselfPass() {
        $ioc = new IocImpl();
        $ioc->register($ioc);
        $ioc2 = $ioc->get(Ioc::class);
        $this->assertSame($ioc,$ioc2);
    }


    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterNullFails() {
        $ioc = new IocImpl();
        $ioc->register(null);
    }

    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterNonExistentClassFails() {
        $ioc = new IocImpl();
        $ioc->register(null);
    }


    public function testA() {
        $ioc = new IocImpl();

        $ioc->register(A::class);
        $a = $ioc->get(IA::class);
        $this->assertNotNull($a);
        $this->assertInstanceOf(IA::class,$a);
        $allA = $ioc->getAll(IA::class);
        $this->assertNotNull($allA);
        $this->assertArrayHasKey(0,$allA);
        $this->assertInstanceOf(IA::class,$allA[0]);
    }

    public function testMultipleRegistrationsOfSameInterfaceReturnedInGetAllPass() {
        $ioc = new IocImpl();

        $ioc->register(A::class);
        $ioc->register(AB::class);
        $allA = $ioc->getAll(IA::class);
        $this->assertNotNull($allA);
        $this->assertArrayHasKey(0,$allA);
        $this->assertArrayHasKey(1,$allA);

        $b = $ioc->get(IB::class);
        $this->assertInstanceOf(IB::class,$b);

        $a = $ioc->get(IA::class);
        $this->assertInstanceOf(IA::class,$a);
    }

    public function testRegisterClassWithMultipleImplementationsReturnsSameInstance() {
        $ioc = new IocImpl();
        $ioc->register(AB::class);
        $a = $ioc->get(IA::class);
        $this->assertInstanceOf(IA::class,$a);

        $b = $ioc->get(IB::class);
        $this->assertInstanceOf(IB::class,$b);

        $this->assertSame($a,$b);
    }

    public function testResolutionOrderFirstAndLastPass() {
        $ioc = new IocImpl();
        $a1 = new A();
        $a2 = new A();
        $a3 = new A();
        $ioc->register($a1);
        $ioc->register($a2);
        $ioc->register($a3);

        $this->assertSame($a1,$ioc->get(IA::class,null,Ioc::RESOLUTION_ORDER_FIRST));
        $this->assertSame($a3,$ioc->get(IA::class,null,Ioc::RESOLUTION_ORDER_LAST));
    }

    public function testResolutionWithClosureReturnsClassNamePass() {
        $ioc = new IocImpl(function(\ReflectionParameter $param, $config) {
            return A::class;
        });
        $ioc->register(DependentA::class);

        $dependentA = $ioc->get(IDependentA::class);
        $this->assertNotNull($dependentA);
        $this->assertInstanceOf(IA::class,$dependentA->getA());
    }

    public function testInstantiate() {
        $ioc = new IocImpl();
        $a = new A();
        $ioc->register($a);

        $dependentA = $ioc->instantiate(DependentA::class);
        $this->assertNotNull($dependentA);
        $this->assertSame($a,$dependentA->getA());
    }
}