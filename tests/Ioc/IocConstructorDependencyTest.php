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

        $dependentA = $ioc->create(IDependentA::class);
        $this->assertNotNull($dependentA);
        $a = $ioc->create(IA::class);
        $this->assertSame($a,$dependentA->getA());
    }

    public function testResolveDependencyViaConstructorReversedOrder() {
        $ioc = new IocImpl();

        $ioc->register(DependentA::class);
        $ioc->register(A::class);
        $a = $ioc->create(IA::class);
        $dependentA = $ioc->create(IDependentA::class);

        $this->assertNotNull($dependentA);
        $this->assertNotNull($a);
        $this->assertSame($a,$dependentA->getA());
    }
}