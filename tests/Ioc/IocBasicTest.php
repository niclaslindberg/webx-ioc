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

    public function testA() {
        $ioc = new IocImpl();

        $ioc->register(A::class);
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

        $ioc->register(A::class);
        $ioc->register(AB::class);
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
        $ioc->register(AB::class);
        $a = $ioc->create(IA::class);
        $this->assertInstanceOf(IA::class,$a);

        $b = $ioc->create(IB::class);
        $this->assertInstanceOf(IB::class,$b);

        $this->assertSame($a,$b);
    }

}