<?php

namespace WebX\Ioc;

use WebX\Ioc\TestClasses1\ClassA;
use WebX\Ioc\TestClasses1\ClassB;
use WebX\Ioc\TestClasses1\InterfaceA;
use WebX\Ioc\TestClasses1\InterfaceB;
use WebX\Ioc\Impl\IocImpl;

class IocFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateClassWithoutDependencies_Pass() {
        $ioc = new IocImpl();
        $ioc->register(ClassA::class, ["factory"=>function(){
            return new ClassA();
        }]);

        $a = $ioc->get(InterfaceA::class);
        $this->assertNotNull($a);
        $this->assertInstanceOf(InterfaceA::class,$a);
        $a2 = $ioc->get(InterfaceA::class);

        $this->assertSame($a,$a2);
        $this->assertEquals(3,$a->add(1,2));
    }


    public function testCreateClassWithDependencies_Pass() {
        $ioc = new IocImpl();
        $ioc->register(ClassA::class);
        $ioc->register(ClassB::class,["factory"=>function(InterfaceA $a){
            return new ClassB($a);
        }]);

        $a = $ioc->get(InterfaceA::class);
        $this->assertNotNull($a);
        $b = $ioc->get(InterfaceB::class);
        $this->assertNotNull($b);
        $this->assertInstanceOf(InterfaceA::class,$a);
        $this->assertInstanceOf(InterfaceB::class,$b);


        $this->assertSame($a,$b->getA());


    }

    public function testCreateClassWithDependenciesAndParameter_Pass() {
        $ioc = new IocImpl();
        $testValue = "Hello";
        $ioc->register(ClassA::class);
        $ioc->register(ClassB::class,["factory"=>function(InterfaceA $a,$value){
            return new ClassB($a,$value);
        },"parameters"=>["value"=>$testValue]]);

        $a = $ioc->get(InterfaceA::class);
        $this->assertNotNull($a);
        $b = $ioc->get(InterfaceB::class);
        $this->assertNotNull($b);
        $this->assertEquals($testValue,$b->getValue());
        $this->assertInstanceOf(InterfaceA::class,$a);
        $this->assertInstanceOf(InterfaceB::class,$b);


        $this->assertSame($a,$b->getA());


    }

}