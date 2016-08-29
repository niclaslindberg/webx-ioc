<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocBasicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterInterfaceWithoutFactoryFails() {
        $ioc = new IocImpl();
        $ioc->register(IA::class);
        $a = $ioc->get(IA::class);

    }

    public function testRegisterInterfaceWithFactoryPass() {
        $ioc = new IocImpl();
        $ioc->register(IA::class,["factory"=>function(){
            return new A();
        }]);
        $a = $ioc->get(IA::class);
        $this->assertNotNull($a);
        $this->assertInstanceOf(IA::class,$a);
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

    public function testResolutionWithClosureInstantiatesClassWithIocPass() {
        $ioc = new IocImpl(function(IocNonResolvable $nonResolvable, Ioc $ioc) {
            return $ioc->instantiate(A::class);
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


    public function testResolveClassByClassName() {
        $ioc = new IocImpl();
        $a = new A();
        $ioc->register($a,["registerClass"=>true]);

        $this->assertSame($a,$ioc->get(A::class));

    }

    public function testInstantiateWithConcreteClassDependency() {

        $ioc = new IocImpl();
        $b = new B();
        $ioc->register($b,["registerClass"=>true]);
        $a = $ioc->instantiate(DependentA_ConcreteB::class);
        $this->assertInstanceOf(DependentA_ConcreteB::class,$a);
        $this->assertSame($b,$a->getB());
    }

}