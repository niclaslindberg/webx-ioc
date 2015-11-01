<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class DependentA implements IDependentA
{
    use ToStringTrait;

    private $a;

    public function __construct(IA $a) {
        $this->a = $a;
    }

    public function getA()
    {
       return $this->a;
    }
}