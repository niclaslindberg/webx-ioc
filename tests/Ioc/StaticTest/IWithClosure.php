<?php

namespace WebX\Ioc\StaticTest;


interface IWithClosure
{
    public function doSomethingWithoutDefault(\Closure $closure);
    public function doSomethingWithDefault(\Closure $closure = null);
}