<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class DependentAB implements IDependentAB
{
    use ToStringTrait;

    private $a;
    private $b;

    public function __construct(IA $a, IB $b) {
        $this->a = $a;
        $this->b = $b;
    }

    public function getA()
    {
       return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

}