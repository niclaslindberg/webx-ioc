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

        $ioc->register($a1,["id"=>"instance1"]);
        $ioc->register($a2,["id"=>"instance2"]);

        $ioc->register(DependentA::class,["mappings"=>["a"=>"instance2"]]);

        $dependentA = $ioc->get(IDependentA::class);

        $this->assertSame($a2,$dependentA->getA());
        $this->assertNotSame($a1,$dependentA->getA());
    }

    public function testRegisterTwoNamedClassNames() {
        $ioc = new IocImpl();

        $ioc->register(A::class,["id"=>"a1"]);
        $ioc->register(A::class,["id"=>"a2"]);

        $ioc->register(DependentA::class,["id"=>"d1","mappings"=>["a"=>"a1"]]);
        $ioc->register(DependentA::class,["id"=>"d2","mappings"=>["a"=>"a2"]]);

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

        $ioc->register(AB::class,["id"=>"instance1"]);
        $ioc->register(A::class, ["id"=>"instance1"]);
    }

    public function testRegisterTwoNamedImplsFindByIdPasses() {
        $ioc = new IocImpl();

        $a1 = new A();
        $a2 = new A();

        $ioc->register($a1,["id"=>"instance1"]);
        $ioc->register($a2,["id"=>"instance2"]);

        $a2b = $ioc->get(IA::class,"instance2");

        $this->assertSame($a2,$a2b);
        $this->assertNotSame($a1,$a2b);
    }

    public function testRegisterUnknownCallsResolverWithId() {
        $idHistory = [];
        $resolver = function(\ReflectionParameter $param, $config) use(&$idHistory) {
            $idHistory[] = $config["id"];
            return $config["id"];
        };

        $ioc = new IocImpl($resolver);

        $ioc->register(UnknownVarNoDefault::class,["id"=>"i1"]);
        $ioc->register(UnknownVarNoDefault::class,["id"=>"i2"]);

        $i1 = $ioc->get(IUnknownVar::class,"i1");
        $i2 = $ioc->get(IUnknownVar::class,"i2");

        $this->assertEquals(2,count($idHistory));
        $this->assertEquals("i1",$idHistory[0]);
        $this->assertEquals("i2",$idHistory[1]);


    }

}