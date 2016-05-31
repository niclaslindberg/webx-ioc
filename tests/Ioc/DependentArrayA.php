<?php
/**
 * User: niclas
 * Date: 5/31/16
 * Time: 4:26 PM
 */

namespace WebX\Ioc;


class DependentArrayA implements IDependentArrayA
{
    use ToStringTrait;

    private $a;

    public function __construct(array $a) {
        $this->a = $a;
    }

    public function getAs()
    {
        return $this->a;
    }


}