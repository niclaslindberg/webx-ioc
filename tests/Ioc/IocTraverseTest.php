<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocTraverseTest extends \PHPUnit_Framework_TestCase
{


    public function testTraversePass() {

        $ioc = new IocImpl();

        $ioc->register(A2::class,["traverse"=>true]);

        $a_1 = $ioc->get(IA::class);
        $a_11 = $ioc->get(IA2::class);
        $this->assertSame($a_1,$a_11);
    }


}