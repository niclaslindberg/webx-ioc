<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class DependentA_ConcreteB
{
    use ToStringTrait;

    private $b;

    public function __construct(B $b) {
        $this->b = $b;
    }

    public function getB()
    {
       return $this->b;
    }
}