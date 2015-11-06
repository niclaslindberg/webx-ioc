<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class UnknownVarNoDefault implements IUnknownVar
{
    use ToStringTrait;

    private $var;

    public function __construct($var) {
        $this->var = $var;
    }

    public function getVar()
    {
       return $this->var;
    }
}