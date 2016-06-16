<?php


namespace WebX\Ioc\TestClasses1;




class ClassA implements InterfaceA
{
    public function add($a, $b)
    {
        return $a+$b;
    }
}