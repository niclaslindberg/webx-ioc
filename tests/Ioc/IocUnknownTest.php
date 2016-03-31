<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocUnknownTest extends \PHPUnit_Framework_TestCase
{

    public function testUnknown_NoResolver_WithDefault_Pass() {
        $ioc = new IocImpl();
        $ioc->register(UnknownArrayWithDefault::class);
        $unknown = $ioc->get(IUnknownArray::class);
    }

    /**
     * @expectedException \WebX\Ioc\IocException
     */
    public function testUnknown_NoResolver_NoDefault_Fails() {
        $ioc = new IocImpl();
        $ioc->register(UnknownArrayNoDefault::class);
        $unknown = $ioc->get(IUnknownArray::class);
    }


    public function testUnknownFailsWithClosureToDeliveryArray() {
        $array = [1];
        $closure = function(IocNonResolvable $nonResolvable) use ($array) {
            return $array;
        };

        $ioc = new IocImpl($closure);
        $ioc->register(UnknownArrayNoDefault::class);

        $unknown = $ioc->get(IUnknownArray::class);
        $this->assertNotNull($unknown);
        $this->assertSame($array,$unknown->getArray());
    }
}