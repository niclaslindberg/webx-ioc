<?php

namespace WebX\Ioc;

use WebX\Ioc\TestClasses1\ClassA;
use WebX\Ioc\TestClasses1\ClassB;
use WebX\Ioc\TestClasses1\InterfaceA;
use WebX\Ioc\TestClasses1\InterfaceB;
use WebX\Ioc\Impl\IocImpl;

class IocInvokeTest extends \PHPUnit_Framework_TestCase
{

    public function testInvokeClosure_Pass() {
        $ioc = new IocImpl();
        $ioc->register(ClassA::class);

        $result = $ioc->invoke(function(InterfaceA $a){
           return $a;
        });
        $this->assertNotNull($result);
        $this->assertInstanceOf(InterfaceA::class,$result);

        $a = $ioc->get(InterfaceA::class);
        $this->assertSame($a,$result);
    }

    public function testInvokeClosureWithMappings_Pass() {
        $ioc = new IocImpl();
        $ioc->register(ClassA::class,["id"=>"1"]);
        $ioc->register(ClassA::class,["id"=>"2"]);

        $a1 = $ioc->get(InterfaceA::class,"1");
        $a2 = $ioc->get(InterfaceA::class,"2");

        $result1 = $ioc->invoke(function(InterfaceA $a){
            return $a;
        },["mappings"=>["a"=>"1"]]);
        $this->assertNotNull($result1);
        $this->assertSame($a1,$result1);

        $result2 = $ioc->invoke(function(InterfaceA $a){
            return $a;
        },["mappings"=>["a"=>"2"]]);
        $this->assertNotNull($result2);
        $this->assertSame($a2,$result2);

    }

    public function testInvokeClosureWithMappingsTypes() {
        $ioc = new IocImpl();
        $ioc->register(ClassA::class,["id"=>"1"]);
        $ioc->register(ClassA::class,["id"=>"2"]);

        $result = $ioc->invoke(function(array $as){
            return $as;
        },["types"=>["as"=>InterfaceA::class]]);
        $this->assertNotNull($result);
        $this->assertSame(2,count($result));
        $this->assertContainsOnlyInstancesOf(InterfaceA::class,$result);

    }

}