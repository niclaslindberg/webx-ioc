<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class A implements IA
{
    use ToStringTrait;

    public function doA()
    {
        return "A";
    }

}