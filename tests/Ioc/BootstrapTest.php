<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;
use WebX\Ioc\Util\Bootstrap;

class BootstrapcTest extends \PHPUnit_Framework_TestCase
{

    public function testBootstrapReturnsInstance() {
        $ioc1 = Bootstrap::ioc();
        $this->assertNotNull($ioc1);

        $ioc2 = Bootstrap::ioc();
        $this->assertNotNull($ioc2);

        $this->assertSame($ioc1,$ioc2);
        $this->assertInstanceOf(Ioc::class,$ioc1);

    }
}