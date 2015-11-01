<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocConstructorDependencyTest extends \PHPUnit_Framework_TestCase
{

    public function testResolveDependencyViaConstructor() {
        $ioc = new IocImpl();

        $ioc->register(A::class);
        $ioc->register(DependentA::class);

        $dependentA = $ioc->get(IDependentA::class);
        $this->assertNotNull($dependentA);
        $a = $ioc->get(IA::class);
        $this->assertSame($a,$dependentA->getA());
    }

    public function testResolveDependencyViaConstructorReversedOrder() {
        $ioc = new IocImpl();

        $ioc->register(DependentA::class);
        $ioc->register(A::class);
        $a = $ioc->get(IA::class);
        $dependentA = $ioc->get(IDependentA::class);

        $this->assertNotNull($dependentA);
        $this->assertNotNull($a);
        $this->assertSame($a,$dependentA->getA());
    }

    public function testResolveMultipleDependencyPass() {
        $ioc = new IocImpl();

        $ioc->register(DependentAB::class);
        $ioc->register(A::class);
        $ioc->register(B::class);
        $a = $ioc->get(IA::class);
        $b = $ioc->get(IB::class);

        $dependentAB = $ioc->get(IDependentAB::class);

        $this->assertNotNull($dependentAB);
        $this->assertNotNull($a);
        $this->assertNotNull($b);
        $this->assertSame($a,$dependentAB->getA());
        $this->assertSame($b,$dependentAB->getB());
    }
}