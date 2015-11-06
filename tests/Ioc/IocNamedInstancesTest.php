<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocNamedInstancesTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterTwoNamedInstances() {
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

    public function testRegisterTwoNamedClassNames() {
        $ioc = new IocImpl();

        $ioc->register(A::class,"a1");
        $ioc->register(A::class,"a2");

        $ioc->register(DependentA::class,"d1",["a"=>"a1"]);
        $ioc->register(DependentA::class,"d2",["a"=>"a2"]);

        $d1 = $ioc->get(IDependentA::class,"d1");
        $d1b = $ioc->get(IDependentA::class,"d1");
        $d2 = $ioc->get(IDependentA::class,"d2");

        $this->assertNotSame($d1,$d2);
        $this->assertNotSame($d1->getA(),$d2->getA());
        $this->assertSame($d1,$d1b);
    }



    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testRegisterTwoImplsWithCollidingIds() {
        $ioc = new IocImpl();

        $ioc->register(AB::class,"instance1");
        $ioc->register(A::class, "instance1");
    }

    public function testRegisterTwoNamedImplsFindByIdPasses() {
        $ioc = new IocImpl();

        $a1 = new A();
        $a2 = new A();

        $ioc->register($a1,"instance1");
        $ioc->register($a2,"instance2");

        $a2b = $ioc->get(IA::class,"instance2");

        $this->assertSame($a2,$a2b);
        $this->assertNotSame($a1,$a2b);
    }

    public function testRegisterUnknownCallsResolverWithId() {
        $idHistory = [];
        $resolver = function(\ReflectionParameter $param, $id) use(&$idHistory) {
            $idHistory[] = $id;
            return $id;
        };

        $ioc = new IocImpl($resolver);

        $ioc->register(UnknownVarNoDefault::class,"i1");
        $ioc->register(UnknownVarNoDefault::class,"i2");

        $i1 = $ioc->get(IUnknownVar::class,"i1");
        $i2 = $ioc->get(IUnknownVar::class,"i2");

        $this->assertEquals(2,count($idHistory));
        $this->assertEquals("i1",$idHistory[0]);
        $this->assertEquals("i2",$idHistory[1]);


    }

}