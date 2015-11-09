<?php

namespace WebX\Ioc;

use WebX\Ioc\Impl\IocImpl;
use WebX\Ioc\IocException;

class IocParameterTest extends \PHPUnit_Framework_TestCase
{


    public function testWithParameterPass() {

        $ioc = new IocImpl();

        $param = "123";

        $ioc->register(UnknownVarNoDefault::class,["parameters"=>["var"=>$param]]);

        $unknown = $ioc->get(IUnknownVar::class);
        $this->assertNotNull($unknown);
        $this->assertSame($param,$unknown->getVar());
    }
}