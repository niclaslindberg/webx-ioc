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

}