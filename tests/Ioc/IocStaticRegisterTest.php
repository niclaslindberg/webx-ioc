<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\StaticTest\CStatic;
class IocStaticRegisterTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticRegister1Arg_Pass() {
        $ioc = new IocImpl();
        $ioc->register(C::class);
        $ioc->initStatic(CStatic::class,"setC");

        $cStatic = new CStatic();
        $this->assertInstanceOf(IC::class,CStatic::$c);

        $sum = $cStatic->executeAdd(1,2);
        $this->assertEquals(3,$sum);

        $diff = $cStatic->executeSub(1,2);
        $this->assertEquals(-1,$diff);
    }

    public function testStaticRegister2Arg_Pass() {
        $ioc = new IocImpl();
        $ioc->register(C::class);
        $ioc->register(A::class);
        $ioc->initStatic(CStatic::class,"initAB");

        $cStatic = new CStatic();
        $this->assertInstanceOf(IC::class,CStatic::$c);

        $sum = $cStatic->executeAdd(1,2);
        $this->assertEquals(3,$sum);

        $res = $cStatic->executeDoA();
        $this->assertEquals("A",$res);
    }

    public function testStaticRegisterWithClosures_Pass() {
        $ioc = new IocImpl();
        $ioc->initStatic(CStatic::class,"initWithClosure");

        $this->assertNotNull(CStatic::$withClosure);
    }

}