<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class C implements IC
{
    public function sub($a, $b)
    {
        return $a-$b;
    }

    use ToStringTrait;

    public function add($a,$b)
    {
        return $a+$b;
    }

}