<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocNamedInstancesTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterTwoNamedImpls() {
        $ioc = new IocImpl();

        $a1 = new A();
        $a2 = new A();

        $ioc->register($a1,"instance1");
        $ioc->register($a2,"instance2");

        $ioc->register(DependentA::class,null,["a"=>"instance2"]);

        $dependentA = $ioc->get(IDependentA::class);

        $this->assertSame($a2,$dependentA->getA());
        $this->assertNotSame($a1,$dependentA->getA());
    }

    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterTwoImplsWithCollidingIds() {
        $ioc = new IocImpl();

        $ioc->register(AB::class,"instance1");
        $ioc->register(A::class, "instance1");
    }
}