<?php


namespace WebX\Ioc\TestClasses1;




class ClassB implements InterfaceB
{

    /**
     * @var A
     */
    private $a;

    /**
     * @var mixed
     */
    private $value;

    public function __construct(InterfaceA $a,$value=null)
    {
        $this->a = $a;
        $this->value = $value;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getValue() {
        return $this->value;
    }

}