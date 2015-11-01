<?php

namespace WebX\Ioc;
/**
 * Inversion Of Control container bootstrap.
 * Interface Ioc
 * @package WebX\Ioc
 */
use WebX\Ioc\Impl\IocImpl;

final class IocBootstrap {

    private static $ioc;


    /**
     * static accessor method for the shared IOC instance.
     * @return IocImpl
     */
    public static function get() {
        if(!self::$ioc) {
            self::$ioc = new IocImpl();
        }
        return self::$ioc;
    }

}