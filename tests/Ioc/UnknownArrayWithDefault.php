<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


class UnknownArrayWithDefault implements IUnknownArray
{
    use ToStringTrait;

    private $array;

    public function __construct(array $array = array()) {
        $this->array = $array;
    }

    public function getArray()
    {
       return $this->array;
    }
}