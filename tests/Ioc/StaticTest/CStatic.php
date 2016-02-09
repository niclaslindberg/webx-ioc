<?php


namespace WebX\Ioc\StaticTest;

use InterfaceWithStaticMethod;
use WebX\Ioc\IA;
use WebX\Ioc\ToStringTrait;
use WebX\Ioc\IC;



class CStatic
{
    use ToStringTrait;

    /**
     * @var IC
     */
    public static $c;

    /**
     * @var IA
     */
    public static $a;

    public static $withClosure;


    public static function setC(IC $c) {
        self::$c = $c;
    }

    public static function initAB(IC $c, IA $a) {
        self::$c = $c;
        self::$a = $a;
    }

    public static function initWithClosure(IWithClosure $withClosure) {
        self::$withClosure = $withClosure;
    }

    public function executeDoA() {
        return self::$a->doA();
    }

    public function executeAdd($a,$b) {
        return self::$c->add($a,$b);
    }

    public function executeSub($a,$b) {
        return self::$c->sub($a,$b);
    }

}