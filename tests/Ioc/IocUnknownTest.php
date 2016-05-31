<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocUnknownTest extends \PHPUnit_Framework_TestCase
{

 


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