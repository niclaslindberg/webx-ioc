<?php

namespace WebX\Ioc\Util;
use WebX\Ioc\Impl\IocImpl;

/**
 * Class with static accessor method get a hold a single IOC instance.
 * @package WebX\Ioc\Util
 */
final class Bootstrap {

    private static $ico;

    private function Bootstrap() {}

    /**
     * Initializes the IOC instance with an optional resolver function.
     * The init function also registers the single IOC instance with itself allowing the container to resolve it self when asked for instance of Ioc::class:
     * <code>
     *  $ioc = $ioc->get(Ioc::class);
     * </code>
     * @param \Closure|null $resolver
     * @throws \WebX\Ioc\IocException
     */
    public static function init(\Closure $resolver = null) {
        $ico = new IocImpl($resolver);
        $ico->register($ico);
        self::$ico = $ico;
    }

    /**
     * Accessor method to get a hold of the Ioc instance.
     * If the Bootstrap has not been initalized by a call to init() this method automatically initializes a container without a resolver function.
     * @return mixed
     */
    public static function ioc() {
        if(!self::$ico) {
            self::init(null);
        }
        return self::$ico;
    }
}