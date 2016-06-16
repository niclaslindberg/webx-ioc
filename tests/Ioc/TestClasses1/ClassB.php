<?php


namespace WebX\Ioc\TestClasses1;




class ClassB implements InterfaceB
{

    /**
     * @var A
     */
    private $a;

    public function __construct(InterfaceA $a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }

}