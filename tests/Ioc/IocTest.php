<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterInterfaceFails() {
        $ioc = new IocImpl();
        try {
            $ioc->register(IA::class);
            $this->fail("Should not be able to register an interface.");
        } catch(IocException $e) {}
    }

    public function testA() {
        $ioc = new IocImpl();

        $ioc->register(ClassA::class);
        $a = $ioc->create(IA::class);
        $this->assertNotNull($a);
        $this->assertInstanceOf(IA::class,$a);
        $allA = $ioc->createAll(IA::class);
        $this->assertNotNull($allA);
        $this->assertArrayHasKey(0,$allA);
        $this->assertInstanceOf(IA::class,$allA[0]);
    }

    public function testMultipleRegistrationsOfSameInterfaceReturnedInCreateAllPass() {
        $ioc = new IocImpl();

        $ioc->register(ClassA::class);
        $ioc->register(ClassAB::class);
        $allA = $ioc->createAll(IA::class);
        $this->assertNotNull($allA);
        $this->assertArrayHasKey(0,$allA);
        $this->assertArrayHasKey(1,$allA);

        $b = $ioc->create(IB::class);
        $this->assertInstanceOf(IB::class,$b);

        $a = $ioc->create(IA::class);
        $this->assertInstanceOf(IA::class,$a);
    }

    public function testRegisterClassWithMultipleImplementationsReturnsSameInstance() {
        $ioc = new IocImpl();
        $ioc->register(ClassAB::class);
        $a = $ioc->create(IA::class);
        $this->assertInstanceOf(IA::class,$a);

        $b = $ioc->create(IB::class);
        $this->assertInstanceOf(IB::class,$b);

        $this->assertSame($a,$b);

    }

}